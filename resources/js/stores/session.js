import { defineStore } from "pinia";
import axios from "axios";
import { debugLog } from "../utils/logger.js";

export const useSessionStore = defineStore("session", {
    state: () => ({
        user: null,
        initialized: false,
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => state.user !== null,
        service: (state) => state.user?.service_sigle ?? null,
    },

    actions: {
        async fetchSession() {
            this.loading = true;
            debugLog("session", "Récupération de la session.");

            try {
                const response = await axios.get("/session");
                this.user = response.data.user;
                this.initialized = true;
                debugLog("session", "Session récupérée.", {
                    service: this.service,
                });

                return this.user;
            } catch (error) {
                if (error.response?.status === 401) {
                    this.user = null;
                    this.initialized = true;
                }
                debugLog("session", "Échec de récupération de la session.", {
                    status: error.response?.status ?? "network",
                });

                throw error;
            } finally {
                this.loading = false;
            }
        },

        async login(credentials) {
            this.loading = true;
            debugLog("session", "Tentative de connexion.");

            try {
                const response = await axios.post("/login", credentials);
                this.user = response.data.user;
                this.initialized = true;
                debugLog("session", "Connexion réussie.", {
                    service: this.service,
                });

                return this.user;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;
            debugLog("session", "Déconnexion demandée.");

            try {
                await axios.delete("/session");
                this.user = null;
                this.initialized = true;
                debugLog("session", "Déconnexion réussie.");
            } finally {
                this.loading = false;
            }
        },

        async updatePassword(credentials) {
            this.loading = true;
            debugLog("session", "Mise à jour du mot de passe demandée.");

            try {
                const response = await axios.patch("/session/password", credentials);
                debugLog("session", "Mot de passe mis à jour.");

                return response.data;
            } finally {
                this.loading = false;
            }
        },
    },
});
