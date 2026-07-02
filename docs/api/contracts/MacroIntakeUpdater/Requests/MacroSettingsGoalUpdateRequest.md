# MacroSettingsGoalUpdateRequest contract.

This document defines the contract for the `MacroSettingsGoalUpdateRequest`.

## Versioning
v1 - 02/07/2026 - Document created and contract defined.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 02/07/2026 | N/A | V1.1 | N/A |

## Contract

### Endpoint
/api/settings

### Structure
{
  "newCalories": 3500,
  "newProtein": 200,
  "newCarbs": 300,
  "newFats": 90,
  "newFiber": 40
}

### Misc
- **Method:** POST.
- **Requires auth:** Yes.
- **Auth type:** JWT.

### Field description

In this section we describe the contract fields.

1. newProtein - Goal for protein grams.
2. newCarbs - Goal for carbohydrates grams.
3. newFats - Goal for fats grams.
4. newFiber - Goal for fiber grams.
5. newCalories - Goal for calories grams.
