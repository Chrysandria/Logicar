import "./bootstrap";
import "bootstrap";

import login from "./component/login.vue";
import snr from "./component/snr.vue";
import other_service from "./component/other_service.vue";

import { createApp } from "vue";
import Swal from "sweetalert2";
window.Swal = Swal;

export const Login = createApp(login);
export const SNR = createApp(snr);
export const OS = createApp(other_service);
