<template>
    <div class="dashboard-wrapper">
        <header class="navbar">
            <h2 class="logo">AKRK Finanças</h2>
            <button @click="handleLogout" class="btn-logout" :disabled="loadingLogout">
                {{ loadingLogout ? 'Saindo...' : 'Sair' }}
            </button>
        </header>

        <main class="content">
            <h1 class="page-title">Meu Painel</h1>

            <div class="cards-grid">
                <div class="card bg-navy">
                    <h3>Total Gasto - Mês Atual</h3>
                    <p class="value">
                        {{ loadingData ? '...' : formatCurrency(totalMonth) }}
                    </p>
                </div>

                <div class="card bg-white">
                    <h3>Atalhos Rápidos</h3>
                    <div class="actions">
                        <router-link to="/nova-despesa" class="btn-action" style="text-decoration: none; display: inline-block; text-align: center;">Nova Despesa</router-link>
                        <router-link to="/despesas" class="btn-action-outline" style="text-decoration: none; display: inline-block; text-align: center;">Ver Despesas</router-link>
                        <router-link to="/categorias" class="btn-action-outline" style="text-decoration: none; display: inline-block; text-align: center;">Categorias</router-link>
                    </div>
                </div>
            </div>

            <div class="list-section">
                <h3>Últimas 5 Despesas</h3>

                <div v-if="loadingData" class="empty-state">
                    Buscando suas despesas...
                </div>

                <div v-else-if="recentExpenses.length === 0" class="empty-state">
                    Você ainda não tem despesas cadastradas.
                </div>

                <div v-else class="expense-list">
                    <div v-for="expense in recentExpenses" :key="expense.id" class="expense-item">
                        <div class="expense-info">
                            <strong>{{ expense.description }}</strong>
                            <span>{{ formatDate(expense.date) }}</span>
                        </div>
                        <div class="expense-amount">
                            {{ formatCurrency(expense.amount) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="list-section">
                <h3>Resumo por Categoria no Mês</h3>

                <div v-if="loadingData" class="empty-state">
                    Montando resumo...
                </div>

                <div v-else-if="categorySummary.length === 0" class="empty-state">
                    Sem gastos por categoria neste mês.
                </div>

                <div v-else class="expense-list">
                    <div v-for="item in categorySummary" :key="item.name" class="expense-item">
                        <div class="expense-info">
                            <strong>{{ item.name }}</strong>
                        </div>
                        <div class="expense-amount">
                            {{ formatCurrency(item.total) }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loadingLogout = ref(false);
const loadingData = ref(true);

const totalMonth = ref(0);
const recentExpenses = ref([]);
const categorySummary = ref([]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatDate = (dateString) => {
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
};

const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/expenses');
        const expenses = response.data;

        recentExpenses.value = expenses.slice(0, 5);

        const today = new Date();
        const currentMonth = today.getMonth() + 1;
        const currentYear = today.getFullYear();

        const monthExpenses = expenses.filter(exp => {
            const [expYear, expMonth] = exp.date.split('-');
            return parseInt(expYear) === currentYear && parseInt(expMonth) === currentMonth;
        });

        totalMonth.value = monthExpenses.reduce((acc, curr) => acc + parseFloat(curr.amount), 0);

        const grouped = monthExpenses.reduce((acc, expense) => {
            const categoryName = expense.category?.name || 'Sem categoria';

            if (!acc[categoryName]) {
                acc[categoryName] = 0;
            }

            acc[categoryName] += parseFloat(expense.amount);
            return acc;
        }, {});

        categorySummary.value = Object.keys(grouped)
            .map(name => ({ name, total: grouped[name] }))
            .sort((a, b) => b.total - a.total);
    } catch (error) {
        if (error.response && error.response.status === 401) {
            router.push('/');
        }
    } finally {
        loadingData.value = false;
    }
};

const handleLogout = async () => {
    loadingLogout.value = true;
    try {
        await axios.post('/api/logout');
    } catch (error) {
        console.log('Erro na API');
    } finally {
        localStorage.removeItem('token');
        loadingLogout.value = false;
        router.push('/');
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
.dashboard-wrapper {
    min-height: 100vh;
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
}

.navbar {
    background-color: white;
    padding: 1rem 5%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.logo {
    color: #0f172a;
    font-weight: 800;
    margin: 0;
}

.btn-logout {
    background: transparent;
    border: 1px solid #e2e8f0;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    color: #64748b;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.2s;
}

.btn-logout:hover {
    background: #fef2f2;
    color: #ef4444;
    border-color: #fecaca;
}

.content {
    padding: 2rem 5%;
    max-width: 1200px;
    margin: 0 auto;
}

.page-title {
    color: #0f172a;
    margin-bottom: 1.5rem;
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.card {
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.bg-navy {
    background-color: #0f172a;
    color: white;
}

.bg-navy h3 {
    color: #cbd5e1;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.bg-navy .value {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    color: #ea580c;
}

.bg-white {
    background-color: white;
    border: 1px solid #e2e8f0;
}

.bg-white h3 {
    color: #64748b;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-action {
    background-color: #ea580c;
    color: white;
    border: none;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}

.btn-action-outline {
    background-color: transparent;
    color: #0f172a;
    border: 1px solid #0f172a;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
}

.list-section {
    margin-bottom: 2rem;
}

.list-section h3 {
    color: #0f172a;
    margin-bottom: 1rem;
}

.empty-state {
    background: white;
    padding: 2rem;
    text-align: center;
    border-radius: 12px;
    color: #64748b;
    border: 1px dashed #cbd5e1;
}

.expense-list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.expense-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.expense-info {
    display: flex;
    flex-direction: column;
}

.expense-info strong {
    color: #0f172a;
    font-size: 1rem;
}

.expense-info span {
    color: #64748b;
    font-size: 0.85rem;
    margin-top: 0.2rem;
}

.expense-amount {
    font-weight: 700;
    color: #ea580c;
    font-size: 1.1rem;
}
</style>
