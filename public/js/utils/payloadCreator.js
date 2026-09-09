export function createIntakePayload(foodId, gramsConsumed) {
    const payload = {
        id: foodId,
        grams: gramsConsumed
    };

    console.log("[INTAKE] sending request →", "/addfood");
    console.log("[INTAKE] payload →", payload);

    fetch("/addfood", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(payload)
    })
        .then(async (res) => {

            console.log("[INTAKE] status →", res.status);

            const text = await res.text();

            console.log("[INTAKE] raw response →", text);

            try {
                const data = JSON.parse(text);

                console.log("[INTAKE] parsed JSON →", data);

                if (data.redirect) {
                    console.log("[INTAKE] redirecting →", data.redirect);

                    window.location.href = data.redirect;
                }

                return data;

            } catch (e) {

                console.error("[INTAKE] response is NOT JSON →", e);

                return text;
            }
        })
        .catch(err => {
            console.error("[INTAKE] request failed →", err);
        });
}
