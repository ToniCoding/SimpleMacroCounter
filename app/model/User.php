<?php

/**
 * Class User
 * Represents a user with credentials, profile info, and timestamps.
 */
class User {
    private ?int $userId;
    private string $username;
    private string $password;
    private string $userAlias;
    private string $email;
    private int $age;
    private string $createdTime;
    private string $lastLogin;
    private bool $isActive;

    /**
     * User constructor.
     *
     * @param string $username User login name.
     * @param string $password User password (hashed expected).
     * @param string $userAlias Display name or alias.
     * @param string $email User email address.
     * @param int $age User age.
     * @param string $createdTime Account creation timestamp.
     * @param string $lastLogin Last login timestamp.
     * @param bool $isActive Account active status.
     */
    public function __construct(string $username, string $password, string $userAlias, string $email, int $age, string $createdTime, string $lastLogin, bool $isActive) {
        $this->userId = null;
        $this->username = $username;
        $this->password = $password;
        $this->userAlias = $userAlias;
        $this->email = $email;
        $this->age = $age;
        $this->createdTime = $createdTime;
        $this->lastLogin = $lastLogin;
        $this->isActive = $isActive;
    }


      public function getUserId(): ?int {
        return $this->userId;
    }

     public function setUserId(int $userId): void {
        $this->userId = $userId;
    }

     public function getUsername(): string {
        return $this->username;
    }

     public function setUsername(string $username): void {
        $this->username = $username;
    }

     public function getPassword(): string {
        return $this->password;
    }

     public function setPassword(string $password): void {
        $this->password = $password;
    }

     public function getUserAlias(): string {
        return $this->userAlias;
    }

     public function setUserAlias(string $userAlias): void {
        $this->userAlias = $userAlias;
    }

     public function getEmail(): string {
        return $this->email;
    }

     public function setEmail(string $email): void {
        $this->email = $email;
    }

     public function getAge(): int {
        return $this->age;
    }

     public function setAge(int $age): void {
        $this->age = $age;
    }

     public function getCreatedTime(): string {
        return $this->createdTime;
    }

     public function setCreatedTime(string $createdTime): void {
        $this->createdTime = $createdTime;
    }

     public function getLastLogin(): string {
        return $this->lastLogin;
    }

     public function setLastLogin(string $lastLogin): void {
        $this->lastLogin = $lastLogin;
    }

     public function getIsActive(): bool {
        return $this->isActive;
    }

     public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }
}
