import axios from "axios";
import { addDays, addWeeks, format, parseISO, startOfWeek } from "date-fns";
import { fr } from "date-fns/locale";
import Swal from "sweetalert2";

const loadingOptions = {
    title: "Veuillez patienter...",
    allowOutsideClick: false,
    allowEscapeKey: false,
    didOpen: () => Swal.showLoading(),
};

export function showLoading(text = "Chargement en cours") {
    return Swal.fire({ ...loadingOptions, text });
}

export function closeLoading() {
    Swal.close();
}

export function showRequestError(error, fallback, title = "Erreur") {
    return Swal.fire({
        icon: "error",
        title,
        text: error.response?.data?.message || fallback,
    });
}

export function showAccessDenied(service) {
    return Swal.fire({
        title: `Accès refusé pour ${service}`,
        text: "Vous n'avez pas les droits nécessaires pour accéder à cet espace.",
        icon: "error",
        timer: 3000,
        showConfirmButton: false,
        timerProgressBar: true,
    });
}

export function showAuthenticationRequired() {
    return Swal.fire({
        title: "Erreur",
        text: "Veuillez vous connecter avant d'accéder à cet espace.",
        icon: "error",
        timer: 3000,
        showConfirmButton: false,
        timerProgressBar: true,
    });
}

export function getPlanningCalendar(date = new Date()) {
    const monday = addWeeks(startOfWeek(date, { weekStartsOn: 1 }), 1);

    return Array.from({ length: 7 }, (_, offset) => {
        const day = addDays(monday, offset);

        return {
            date: format(day, "yyyy-MM-dd"),
            jour: format(day, "EEEE", { locale: fr }),
        };
    });
}

export async function fetchPlannings(date, service) {
    const params = { date };

    if (service) {
        params.service = service;
    }

    const response = await axios.get("/plannings", { params });

    return response.data;
}

export function getLocalIsoDate(date = new Date()) {
    return format(date, "yyyy-MM-dd");
}

export function formatFrenchDate(value) {
    const date = value instanceof Date
        ? value
        : typeof value === "string" && /^\d{4}-\d{2}-\d{2}$/.test(value)
            ? parseISO(value)
            : new Date(value);

    if (Number.isNaN(date.getTime())) {
        throw new RangeError(`Date invalide : ${value}`);
    }

    return format(date, "P", { locale: fr });
}

export function startFrenchClock(onTick) {
    const update = () => {
        onTick(format(new Date(), "dd/MM/yyyy HH:mm:ss", { locale: fr }));
    };

    update();

    return window.setInterval(update, 1000);
}

export async function logout(session) {
    const confirmation = await Swal.fire({
        title: "Déconnexion",
        text: "Êtes-vous sûr de vouloir vous déconnecter ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Confirmer",
        cancelButtonText: "Annuler",
    });

    if (!confirmation.isConfirmed) {
        return;
    }

    showLoading("Déconnexion en cours");

    try {
        await session.logout();
        window.location.href = "/login";
    } catch (error) {
        closeLoading();
        showRequestError(
            error,
            "Une erreur est survenue lors de la déconnexion. Veuillez réessayer.",
        );
    }
}

let bootstrapImport;

export async function initTooltips(root = document) {
    bootstrapImport ??= import("bootstrap");
    const { Tooltip } = await bootstrapImport;

    root.querySelectorAll("[data-bs-toggle='tooltip']").forEach((element) => {
        if (!Tooltip.getInstance(element)) {
            new Tooltip(element, { placement: "right" });
        }
    });
}
