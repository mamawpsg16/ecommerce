import routes from './routes.js';
import { createRouter, createWebHistory } from 'vue-router';
import { isAuthenticated as checkIsAuthenticated } from '../helpers/Auth/isAuthenticated.js';
import NProgress from "nprogress/nprogress.js";

const router = createRouter({
    // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
    history: createWebHistory(),
    routes, // short for `routes: routes`
    scrollBehavior() {
        return { left: 0, top: 0 };
    },
})

NProgress.configure({ showSpinner: false, easing: 'ease', speed: 500 });

// GOOD
// This is a navigation guard in Vue Router. It runs before each navigation.
router.beforeEach(async (to, from, next) => {
    NProgress.start();
    const isAuthenticated = await checkIsAuthenticated();
    const isTryingToAccessRestrictedRoute = ['login'].includes(to.name);

    if (isAuthenticated) {
        // If the user is authenticated and trying to access 'login' or 'register', redirect to '/dashboard'.
        if (isTryingToAccessRestrictedRoute) {
            next('/');
        } else {
            // Continue with the navigation for other routes.
            next();
        }
    } else {
        // If the user is not authenticated and is not navigating to 'login', redirect to 'login'.
        if (!isAuthenticated && !isTryingToAccessRestrictedRoute) {
            next('login');
        } else {
            localStorage.removeItem('auth-token');
            // Continue with the navigation for the 'login' route when the user is not authenticated.
            next();
        }
    }
});


router.afterEach((to, from) => {
    NProgress.done();
});
export default router;