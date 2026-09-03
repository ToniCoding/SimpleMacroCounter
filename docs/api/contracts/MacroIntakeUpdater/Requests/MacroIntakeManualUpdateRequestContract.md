# MacroIntakeManualUpdateRequest contract.

This document defines the contract for the `MacroIntakeManualUpdateRequest`.

## Versioning
v1 - 01/07/2026 - Document created and contract defined.

### Document information
| Creation date | Last update | Version creation | Version update |
|---------------|-------------|------------------|----------------|
| 01/07/2026 | N/A | V1.1 | N/A |

## Contract

### Endpoint
/api/v1/modify-macros/{userId}

### Structure
{
  "protein": 100.00,
  "carbs": 100.00,
  "fats": 100.00,
  "fiber": 100.00,
  "intent": "add" // Enum: ADD or REDUCE.
}

### Misc
- **Method:** GET.
- **Requires auth:** Yes.
- **Auth type:** JWT.

### Field description

In this section we describe the contract fields.

1. protein - Protein grams consumed by the user.
2. carbs - Carbohydrates grams consumed by the user.
3. fats - Fats grams consumed by the user.
4. fiber - Fiber grams consumed by the user.
5. intent - The intent of the request. This can be "add" to add the macro-nutrients or "reduce" to substract them.
