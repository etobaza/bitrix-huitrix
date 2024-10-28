<?php
namespace Src;

class Contact
{
    public $name;
    public $lastName;
    public $phone;
    public $email;
    public $type;

    public function __construct($name, $lastName, $phone, $email, $type)
    {
        $this->name = $name;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->email = $email;
        $this->type = $type;
    }

    public function toArray()
    {
        return [
            'NAME' => $this->name,
            'LAST_NAME' => $this->lastName,
            'PHONE' => [
                [
                    'VALUE' => $this->phone,
                    'VALUE_TYPE' => 'WORK',
                ],
            ],
            'EMAIL' => [
                [
                    'VALUE' => $this->email,
                    'VALUE_TYPE' => 'WORK',
                ],
            ],
        ];
    }
}
