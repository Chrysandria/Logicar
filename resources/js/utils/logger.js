const DEBUG_KEY = "logicar:debug";

export function isDebugEnabled() {
    return window.LOGICAR_DEBUG === true || localStorage.getItem(DEBUG_KEY) === "true";
}

export function debugLog(scope, message, details = undefined) {
    if (!isDebugEnabled()) {
        return;
    }

    if (details === undefined) {
        console.debug(`[LogiCar:${scope}] ${message}`);
        return;
    }

    console.debug(`[LogiCar:${scope}] ${message}`, details);
}
