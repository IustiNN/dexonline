<?php

class LexemModel extends BaseObject implements DatedObject {
  public static $_table = 'LexemModel';

  const METHOD_GENERATE = 1;
  const METHOD_LOAD = 2;

  private $lexem = null;
  private $mt = null;                  // ModelType object, but we call it $mt because there is already a DB field called 'modelType'
  private $lexemSources = null;
  private $sources = null;
  private $sourceNames = null;         // Comma-separated list of source names
  private $inflectedForms = null;
  private $inflectedFormMap = null;    // Mapped by various criteria depending on the caller
  private $nonLocInflectionIds = null; // List of ids of inflected forms that are not in LOC

  static function create($modelType, $modelNumber) {
    $lm = Model::factory('LexemModel')->create();
    $lm->modelType = $modelType;
    $lm->modelNumber = $modelNumber;
    $lm->restriction = '';
    $lm->isLoc = false;
    return $lm;
  }

  function getLexem() {
    if ($this->lexem === null) {
      $this->lexem = Lexem::get_by_id($this->lexemId);
    }
    return $this->lexem;
  }

  function setLexem($lexem) {
    $this->lexem = $lexem;
  }

  function hasRestriction($letter) {
    return FlexStringUtil::contains($this->restriction, $letter);
  }

  function getModelType() {
    if ($this->mt === null) {
      $this->mt = ModelType::get_by_code($this->modelType);
    }
    return $this->mt;
  }

  function getLexemSources() {
    if ($this->lexemSources === null) {
      $this->lexemSources = LexemSource::get_all_by_lexemModelId($this->id);
    }
    return $this->lexemSources;
  }

  function setLexemSources($lexemSources) {
    $this->lexemSources = $lexemSources;
  }

  function getSources() {
    if ($this->sources === null) {
      $this->sources = Model::factory('Source')
        ->select('Source.*')
        ->join('LexemSource', 'Source.id = sourceId')
        ->where('LexemSource.lexemModelId', $this->id)
        ->find_many();
    }
    return $this->sources;
  }

  function getSourceNames() {
    if ($this->sourceNames === null) {
      $sources = $this->getSources();
      $results = array();
      foreach($sources as $s) {
        $results[] = $s->shortName;
      }
      $this->sourceNames = implode(', ', $results);
    }
    return $this->sourceNames;
  }

  function getSourceIds() {
    $results = array();
    foreach($this->getSources() as $s) {
      $results[] = $s->id;
    }
    return $results;
  }

  /**
   * Returns an array of InflectedForms. These can be loaded from the disk ($method = METHOD_LOAD)
   * or generated on the fly ($method = METHOD_GENERATE);
   **/
  function getInflectedForms($method) {
    return ($method == self::METHOD_LOAD)
      ? $this->loadInflectedForms()
      : $this->generateInflectedForms();
  }

  function loadInflectedForms() {
    if ($this->inflectedForms === null) {
      $this->inflectedForms = Model::factory('InflectedForm')
        ->where('lexemModelId', $this->id)
        ->order_by_asc('inflectionId')
        ->order_by_asc('variant')
        ->find_many();
    }
    return ($this->inflectedForms);
  }

  function generateInflectedForms() {
    if ($this->inflectedForms === null) {
      $lexem = $this->getLexem();
      $model = FlexModel::loadCanonicalByTypeNumber($this->modelType, $this->modelNumber);
      $inflIds = db_getArray("select distinct inflectionId from ModelDescription where modelId = {$model->id} order by inflectionId");

      try {
        $this->inflectedForms = array();
        foreach ($inflIds as $inflId) {
          $if = $this->generateInflectedFormWithModel($lexem->form, $inflId, $model->id);
          $this->inflectedForms = array_merge($this->inflectedForms, $if);
        }
      } catch (Exception $ignored) {
        // Make a note of the inflection we cannot generate
        $this->inflectedForms = $inflId;
      }
    }
    return $this->inflectedForms;
  }

  function getInflectedFormMap($method) {
    if ($this->inflectedFormMap === null) {
      $ifs = $this->getInflectedForms($method);
      if (is_array($ifs)) {
        $this->inflectedFormMap = InflectedForm::mapByInflectionRank($ifs);
      }
    }
    return $this->inflectedFormMap;
  }

  function loadInflectedFormMap() {
    return $this->getInflectedFormMap(self::METHOD_LOAD);
  }

  function generateInflectedFormMap() {
    return $this->getInflectedFormMap(self::METHOD_GENERATE);
  }

  // Throws an exception if the given inflection cannot be generated
  public function generateInflectedFormWithModel($form, $inflId, $modelId) {
    $ifs = array();
    $mds = Model::factory('ModelDescription')->where('modelId', $modelId)->where('inflectionId', $inflId)
      ->order_by_asc('variant')->order_by_asc('applOrder')->find_many();
 
    $start = 0;
    while ($start < count($mds)) {
      $variant = $mds[$start]->variant;
      $recommended = $mds[$start]->recommended;
      
      // Identify all the md's that differ only by the applOrder
      $end = $start + 1;
      while ($end < count($mds) && $mds[$end]->applOrder != 0) {
        $end++;
      }

      if (ConstraintMap::validInflection($inflId, $this->restriction, $variant)) {
        $inflId = $mds[$start]->inflectionId;
        $accentShift = $mds[$start]->accentShift;
        $vowel = $mds[$start]->vowel;

        // Load and apply all the transforms from $start to $end - 1.
        $transforms = array();
        for ($i = $end - 1; $i >= $start; $i--) {
          $transforms[] = Transform::get_by_id($mds[$i]->transformId);
        }

        $result = FlexStringUtil::applyTransforms($form, $transforms, $accentShift, $vowel);
        if (!$result) {
          throw new Exception();
        }
        $ifs[] = InflectedForm::create($result, $this->id, $inflId, $variant, $recommended);
      }

      $start = $end;
    }
    
    return $ifs;
  }

  // For V1, this loads all lexem models in (V1, VT1)
  public static function loadByCanonicalModel($modelType, $modelNumber, $limit = 0) {
    $q = Model::factory('LexemModel')
      ->table_alias('lm')
      ->select('lm.*')
      ->join('Lexem', 'lm.lexemId = l.id', 'l')
      ->join('ModelType', 'lm.modelType = mt.code', 'mt')
      ->where('mt.canonical', $modelType)
      ->where('lm.modelNumber', $modelNumber)
      ->order_by_asc('l.formNoAccent');

    if ($limit) {
      $q = $q->limit($limit);
    }

    return $q->find_many();
  }

  /**
   * Deletes the lexemModel's old inflected forms, if they exist, then saves the new ones.
   **/
  function regenerateParadigm() {
    if ($this->id) {
      InflectedForm::delete_all_by_lexemModelId($this->id);
    }
    foreach ($this->generateInflectedForms() as $if) {
      $if->lexemModelId = $this->id;
      $if->save();
    }
  }

  /**
   * Adds an isLoc field to every inflected form in the map. Assumes the map already exists.
   **/
  function addLocInfo() {
    // Build a map of inflection IDs not in LOC
    $ids = Model::factory('InflectedForm')
      ->table_alias('i')
      ->select('i.id')
      ->join('LexemModel', 'i.lexemModelId = lm.id', 'lm')
      ->join('ModelType', 'lm.modelType = mt.code', 'mt')
      ->join('Model', 'mt.canonical = m.modelType and lm.modelNumber = m.number', 'm')
      ->join('ModelDescription', 'm.id = md.modelId and i.variant = md.variant and i.inflectionId = md.inflectionId', 'md')
      ->where('md.applOrder', 0)
      ->where('md.isLoc', 0)
      ->where('lm.id', $this->id)
      ->find_array();
    $map = array();
    foreach ($ids as $rec) {
      $map[$rec['id']] = 1;
    }

    // Set the bit accordingly on every inflection in the map
    foreach ($this->inflectedFormMap as $ifs) {
      foreach ($ifs as $if) {
        $if->isLoc = !array_key_exists($if->id, $map);
      }
    }
  }

  function delete() {
    InflectedForm::delete_all_by_lexemModelId($this->id);
    LexemSource::delete_all_by_lexemModelId($this->id);
    // delete_all_by_lexemModelId doesn't work for FullTextIndex because it doesn't have an ID column
    Model::factory('FullTextIndex')->where('lexemModelId', $this->id)->delete_many();
    parent::delete();
  }

  public function __toString() {
    return "{$this->modelType}{$this->modelNumber}{$this->restriction}";
  }

}

?>
