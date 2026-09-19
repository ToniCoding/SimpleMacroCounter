# TodayUserProgress contract

This document defines the contract for the `TodayUserProgress` endpoint.

## Versioning
v1 - 28-06-2026 - Document creation.
v2 - 06/09/2026 - Consolidated Request and Response contracts into a single document.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 28-06-2026 | 06-09-2026 | V1.0 | V1.1 |

## Contract Specification

### Endpoint
`/api/v1/today-progress`

### Overview
- **HTTP Method:** GET
- **Requires Auth:** Yes
- **Auth Type:** Session + JWT

---

## Request

### Body
No body.

---

## Response

### Structure
```json
{
    "todayMacrosProgress": {
        "caloriesProgress": 0,
        "proteinProgress": 0,
        "carbsProgress": 0,
        "fatsProgress": 0,
        "fiberProgress": 0
    },
    "todayUserMacroGrams": {
        "calories": 0,
        "protein": 0,
        "carbs": 0,
        "fats": 0,
        "fiber": 0
    },
    "dailyMacroGramsGoal": {
        "calories": 2000,
        "protein": 120,
        "carbs": 220,
        "fats": 65,
        "fiber": 30
    },
    "weeklyCalorieGoal": 14000,
    "weeklyCalorieConsumption": 0,
    "weeklyCalorieGoalRiskInfo": {
        "risk": 0,
        "risk_color": "green",
        "level": "low",
        "expected_consumption": 0,
        "remaining_budget": 14000
    }
}
