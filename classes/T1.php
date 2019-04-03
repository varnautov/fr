<?php

namespace t1;

class T1
{
    const KEY = 0;
    const VALUE = 1;
    const LEVEL = 2;
    const PARENT = 3;
    const COUNT = 4;
    const OBJECT = 5;

    /**
     * @var array
     */
    public $sample;

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var array
     */
    protected $tree;

    /**
     * @var array
     */
    protected $levels;

    /**
     * @param array $sample
     */
    public function __construct(array $sample)
    {
        $this->sample = $sample;
    }

    public function run()
    {
        $this->tree = [[null, null, null, null, null, null]];
        $lvl = 0;
        $parent = 0;
        $this->extract($this->sample, $lvl, $parent, $this->tree);
        $this->filterAll();
        $this->compose();
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array $elements
     * @param int $lvl
     * @param int $parent
     * @param array $tree
     */
    protected function extract(array $elements, int $lvl, int $parent, array &$tree)
    {
        foreach ($elements as $k => $v) {
            $tree[] = [
                $k,
                is_array($v) ? null : $v,
                $lvl,
                $parent,
                is_array($v) ? count($v) : 0,
                null
            ];
            $i = count($tree) - 1;
            $this->levels[$lvl][] = $i;
            if (is_array($v)) {
                $this->extract($v, $lvl + 1, $i, $tree);
            }
        }
    }

    protected function filterAll()
    {
        for ($l = count($this->levels) - 1; $l >= 0; $l--) {
            foreach ($this->levels[$l] as $li => $i) {
                $this->filter($i, $li);
            }
        }
    }

    /**
     * @param int $i
     * @param int $li
     */
    protected function filter(int $i, int $li)
    {
        $e = $this->tree[$i];
        if (!$e[static::VALUE] && $e[static::COUNT] === 0) {
            $parent = $e[static::PARENT];
            if (!is_null($this->tree[$parent])) {
                $this->tree[$parent][static::COUNT]--;
            }
            $level = $e[static::LEVEL];
            unset($this->levels[$level][$li]);
            unset($this->tree[$i]);
        }
    }

    protected function compose()
    {
        $result = [];
        if (isset($this->levels[0]) && count($this->levels[0]) > 0) {
            // add objects
            for ($l = count($this->levels) - 1; $l >= 0; $l--) {
                foreach ($this->levels[$l] as $i) {
                    $this->addObject($i);
                }
            }
            // compose
            foreach ($this->levels[0] as $i) {
                $sub = json_decode(json_encode($this->tree[$i][static::OBJECT]), true);
                $result = array_merge($result, $sub);
            }
        }
        $this->result = $result;
    }

    protected function addObject(int $i, $link = null)
    {
        $e = $this->tree[$i];

        if ($e[static::OBJECT]) {
            $o = $e[static::OBJECT];
        } else {
            $o = new \stdClass();
        }

        if ($link) {
            $o->{$e[static::KEY]} = $e[static::OBJECT]
                ? (object) array_merge((array) $o->{$e[static::KEY]}, (array) $link)
                : $link;
        } elseif ($e[static::COUNT] === 0) {
            $o->{$e[static::KEY]} = $e[static::VALUE];
        }

        if (!$e[static::OBJECT]) {
            $this->tree[$i][static::OBJECT] = $o;
            if ($e[static::PARENT]) {
                $this->addObject($e[static::PARENT], $o);
            }
        }
    }
}
