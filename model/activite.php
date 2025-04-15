<?php
class Activite
{
    private $id = null;
    private $name = null;
    private $event = null;

    public function __construct($name, $event)
    {
        $this->name = $name;
        $this->event = $event;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getEvent()
    {
        return $this->event;
    }

    public function setEvent($event)
    {
        $this->event = $event;
    }

}
