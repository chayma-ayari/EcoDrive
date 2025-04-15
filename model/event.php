<?php
class Event
{
    private $id = null;
    private $name = null;
    private $description = null;
    private $capacity = null;
    private $location = null;
    private $date = null;
    private $time = null;

    public function __construct($name, $description, $capacity, $location, $date, $time)
    {
        $this->name = $name;
        $this->description = $description;
        $this->capacity = $capacity;
        $this->location = $location;
        $this->date = $date;
        $this->time = $time;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }
}
