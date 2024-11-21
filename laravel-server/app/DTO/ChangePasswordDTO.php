<?php

namespace App\DTO;

class ChangePasswordDTO
{
    public string $oldPassword;
    public string $newPassword;

    public function __construct(string $oldPassword, string $newPassword)
    {
        $this->oldPassword = $oldPassword;
	$this->newPassword = $newPassword;
    }

    public static function fromRequest($request): ChangePasswordDTO
    {
        return new self(
	    $request->oldPassword,
	    $request->newPassword,
	);
    }
}
