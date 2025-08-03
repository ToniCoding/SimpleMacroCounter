<?php

/**
 * Class Streak
 * Represents a user's streak with metadata like level, dates, and status.
 */
class Streak {
    private int $streakOwner;
    private string $streakName;
    private string $streakLevel;
    private DateTime $startDate;
    private int $currentStreak;
    private int $longestStreak;
    private bool $isActive;

    /**
     * Streak constructor.
     *
     * @param int $streakOwner User ID who owns the streak.
     * @param string $streakName Name of the streak.
     * @param string $streakLevel Level or difficulty of the streak.
     * @param DateTime $startDate Date when streak started.
     * @param int $currentStreak Current count of the streak.
     * @param int $longestStreak Longest streak achieved.
     * @param bool $isActive Whether the streak is currently active.
     */
    public function __construct(int $streakOwner, string $streakName, string $streakLevel, DateTime $startDate, int $currentStreak, int $longestStreak, bool $isActive) {
        $this->streakOwner = $streakOwner;
        $this->streakName = $streakName;
        $this->streakLevel = $streakLevel;
        $this->startDate = $startDate;
        $this->currentStreak = $currentStreak;
        $this->longestStreak = $longestStreak;
        $this->isActive = $isActive;
    }

    public function getStreakOwner(): int {
        return $this->streakOwner;
    }

     public function setStreakOwner(int $streakOwner): void {
        $this->streakOwner = $streakOwner;
    }

     public function getStreakName(): string {
        return $this->streakName;
    }

     public function setStreakName(string $streakName): void {
        $this->streakName = $streakName;
    }

     public function getStreakLevel(): string {
        return $this->streakLevel;
    }

     public function setStreakLevel(string $streakLevel): void {
        $this->streakLevel = $streakLevel;
    }

     public function getStartDate(): DateTime {
        return $this->startDate;
    }

     public function setStartDate(DateTime $startDate): void {
        $this->startDate = $startDate;
    }

     public function getCurrentStreak(): int {
        return $this->currentStreak;
    }

     public function setCurrentStreak(int $currentStreak): void {
        $this->currentStreak = $currentStreak;
    }

     public function getLongestStreak(): int {
        return $this->longestStreak;
    }

     public function setLongestStreak(int $longestStreak): void {
        $this->longestStreak = $longestStreak;
    }

     public function getIsActive(): bool {
        return $this->isActive;
    }

     public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }
}
