import axios from 'axios';
import { BASE_PATH } from '../config';

const client = axios.create({
    baseURL: `${BASE_PATH}/api`,
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});
let handlers = {
    flash: () => {},
    onUnauthorized: () => {},
};

export function registerApiHandlers(next) {
    handlers = { ...handlers, ...next };
}

client.interceptors.response.use(
    (response) => {
        const message = response?.data?.message;
        if (message && response.config.method && response.config.method !== 'get') {
            handlers.flash('success', message);
        }
        return response;
    },
    (error) => {
        const status = error.response?.status;
        const data = error.response?.data;

        if (status === 401) {
            handlers.onUnauthorized();
        } else if (status === 422 && data?.errors) {
        } else if (data?.message) {
            handlers.flash('error', data.message);
        } else if (status >= 500) {
            handlers.flash('error', 'Something went wrong. Please try again.');
        }

        return Promise.reject(error);
    }
);

export default client;
