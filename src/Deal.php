<?php

namespace Src;

class Deal
{
    public $title;
    public $type;
    public $source;
    public $status;

    public function __construct($title, $type, $source, $status = 'NEW')
    {
        $this->title = $title;
        $this->type = $type;
        $this->source = $source;
        $this->status = $status;
    }

    public function toArray()
    {
        return [
            'TITLE' => $this->title,
            'TYPE_ID' => $this->type,
            'SOURCE_ID' => $this->source,
            'STAGE_ID' => $this->status,
        ];
    }
}
