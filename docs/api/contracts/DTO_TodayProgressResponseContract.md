# TodayProgressResponseDTO conctract.

This document defines the contract for the `TodayProgressResponseDTO`.

## Versioning
v1 - 28/06/2026 - Document created and contract defined.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 28-06-2026 | N/A | V1.1 | N/A |

## Contract
The contract should respect the following JSON structure:

```JSON
{
	"todayMacrosProgress": {
		"proteinProgress": 0,
		"carbProgress": 0,
		"fatProgress": 0,
		"fiberProgress": 0,
		"calorieProgress": 0
	},
	"todayUserMacroGrams": {
		"proteinGrams": 0,
		"carbGrams": 0,
		"fatGrams": 0,
		"fiberGrams": 0,
		"calories": 0
	},
		"weeklyCalorieGoal": 14700,
		"weeklyCalorieConsumption": 0,
	"weeklyCalorieGoalRiskInfo": {
		"risk": 0,
		"risk_color": "green",
		"level": "low",
		"expected_consumption": 0,
		"remaining_budget": 14700
	}
}
```

### Field description

In this section we describe the contract fields.

1. todayMacrosProgress - Contains the fill percentage for the frontend bars in the home page.
2. todayUserMacroGrams - Contains the macro-nutrients (*grams*) and the calories consumed in the day.
3. weeklyCalorieGoal - The caloric goal for the entire week.
4. weeklyCalorieConsumption - The calorie intake for the week as of today.
5. weeklyCalorieGoalRiskInfo - Contains information about the risk of exceeding the weekly calorie goal like the current risk, expected consumption, risk level and its color to represent it in the UI.
