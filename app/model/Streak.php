<?php

    class Streak {
        private int $streakOwner;
        private string $streakName;
        private string $streakLevel;
        private DateTime $startDate;
        private int $currentStreak;
        private int $longestStreak;
        private bool $isActive;

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
