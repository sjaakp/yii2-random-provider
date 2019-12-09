<?php

/**
 * MIT licence
 * Version 1.0
 * Sjaak Priester, Amsterdam 09-12-2019.
 *
 * RandomProvider - unordered data-provider for Yii 2.0
 */

namespace sjaakp\randomprovider;

use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * Class RandomProvider
 * @package sjaakp\randomprovider
 * @link https://www.warpconduit.net/2011/03/23/selecting-a-random-record-using-mysql-benchmark-results/
 * Notice that this may not work properly if the model has multiple primary key.
 */
class RandomProvider extends ActiveDataProvider
{
    /**
     * @var string|null  name under which RandomProvider's information is stored in session
     * if null: auto-generated
     */
    public $sessionKey;

    /**
     * @var bool  whether RandomProvider resets itself when on the first page
     */
    public $autoReset = true;

    private $_randCmd;

    /**
     * @inheritDoc
     */
    public function init()
    {
        // @link https://stackoverflow.com/questions/580639/how-to-randomly-select-rows-in-sql
        // Notice that this is only tested with mysql!
        $commands = [
            'mysql' => 'RAND()',
            'mysqli' => 'RAND()',
            'mssql' => 'NEWID()',
            'oci' => 'dbms_random.value',
            'pgsql' => 'RANDOM()',
            'sqlite' => 'RANDOM()',
            'sqlite2' => 'RANDOM()',
            'sqlsrv' => 'NEWID()',
        ];
        parent::init();
        $conn = $this->db ?? Yii::$app->db;
        $driver = $conn->driverName;
        $this->_randCmd = $commands[$driver] ?? null;

        if (is_null($this->_randCmd))  {
            $cls = __CLASS__;
            throw new InvalidConfigException("$cls: driver \"$driver\" is not supported.");
        }

        $this->setSort(false);
        if (is_null($this->sessionKey)) $this->sessionKey = '_rnd_' . $this->id;
    }

    /**
     * @inheritDoc
     * @throws \yii\base\InvalidConfigException
     */
    protected function prepareModels()
    {
        /** @var $query yii\db\ActiveQuery */
        /** @var $mc yii\db\ActiveRecord */
        $query = clone $this->query;

        $mc = $query->modelClass;
        $pId = current($mc::primaryKey());
        $session = Yii::$app->session;
        $visited = [];
        $pagination = $this->pagination;
        if ($pagination !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }
            $page = $pagination->page;
            if ($page === 0 && $this->autoReset)  {
                $session->remove($this->sessionKey);
            }
            $visited = Json::decode($session->get($this->sessionKey, '[]'));
            $key = 'p' . $page;
            if (isset($visited[$key])) {   // this page was previously visited, retrieve the same records
                // Notice that the order of the records will probably be different
                return $query->where([ 'in', $pId, $visited[$key] ])->orderBy('')->all($this->db);
            }
            $pageSize = $pagination->pageSize;
            $limit = $pagination->totalCount % $pageSize;
            if ($limit == 0 || $page < ($pagination->pageCount - 1))  {
                $limit = $pageSize;
            }
            $query->limit($limit);
            if (count($visited))   {   // select records not previously selected
                $query->andWhere([
                    'not in', $pId, call_user_func_array("array_merge", $visited)
                ]);
            }
        }
        $query->orderBy(new Expression($this->_randCmd));
        $r = $query->all($this->db);
        if ($pagination !== false)   {
            $newSel = array_column($r, $pId);
            $visited[$key] = $newSel; // add to visited pages
            $session->set($this->sessionKey, Json::encode($visited));
        }
        return $r;
    }
}
