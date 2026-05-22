const STRUCTURED_DATA_ID = "hiroshima-live-structured-data";

export const setStructuredData = (data) => {
    removeStructuredData();

    const script = document.createElement("script");

    script.id = STRUCTURED_DATA_ID;
    script.type = "application/ld+json";
    script.textContent = JSON.stringify(data);

    document.head.appendChild(script);
};

export const removeStructuredData = () => {
    const existingScript = document.getElementById(STRUCTURED_DATA_ID);

    if (existingScript) {
        existingScript.remove();
    }
};
