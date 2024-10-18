<?php

namespace App\DTO;
class RegisterDTO
{
    public string $name;
    public string $email;
    public string $password;

    public function __construct(string $name,string $email,string $password)
    {
        $this->name = $name;
	$this->email = $email;
	$this->password = $password;
    }

    public static function fromRequest($request): RegisterDTO
    {
        return new RegisterDTO(
	    $request->name,
	    $request->email,
	    $request->password
	);
    }
}
