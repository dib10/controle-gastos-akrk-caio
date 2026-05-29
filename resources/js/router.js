import { createRouter, createWebHistory } from 'vue-router';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import Dashboard from './pages/Dashboard.vue';
import Categories from './pages/Categories.vue';
import NewExpense from './pages/NewExpense.vue';
import Expenses from './pages/Expenses.vue';

const routes = [
    {
        path: '/',
        name: 'Login',
        component: Login,
        meta: { publicOnly: true }
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: { publicOnly: true }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/categorias',
        name: 'Categories',
        component: Categories,
        meta: { requiresAuth: true }
    },
    {
        path: '/nova-despesa',
        name: 'NewExpense',
        component: NewExpense,
        meta: { requiresAuth: true }
    },
    {
        path: '/despesas',
        name: 'Expenses',
        component: Expenses,
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');

    if (to.meta.requiresAuth && !token) {
        next('/');
        return;
    }

    if (to.meta.publicOnly && token) {
        next('/dashboard');
        return;
    }

    next();
});

export default router;
