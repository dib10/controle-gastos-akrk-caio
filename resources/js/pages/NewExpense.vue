<template>
    <div class="page-wrapper">
        <header class="navbar">
            <h2 class="logo">AKRK Finanças</h2>
            <nav class="nav-links">
                <router-link to="/dashboard" class="nav-link">Voltar ao Painel</router-link>
            </nav>
        </header>

        <main class="content">
            <div class="header-section">
                <h1 class="page-title">Registrar Despesa</h1>
            </div>

            <div v-if="successMessage" class="success-message">
                {{ successMessage }}
            </div>

            <div v-if="errorMessage" class="error-message">
                {{ errorMessage }}
            </div>

            <div class="card form-card">
                <form @submit.prevent="saveExpense" class="expense-form">
                    <div class="form-group">
                        <label>Descrição</label>
                        <input type="text" v-model="form.description" required placeholder="Ex: Conta de Luz, Supermercado..." />
                    </div>

                    <div class="form-group">
                        <label>Valor (R$)</label>
                        <input type="number" step="0.01" v-model="form.amount" required placeholder="0.00" />
                    </div>

                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" v-model="form.date" :max="maxDate" required />
                    </div>

                    <div class="form-group">
                        <label>Categoria</label>
                        <select v-model="form.category_id" required>
                            <option value="" disabled>Selecione uma categoria...</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <router-link to="/dashboard" class="btn-cancel">Cancelar</router-link>
                        <button type="submit" class="btn-submit" :disabled="loading">
                            {{ loading ? 'Salvando...' : 'Salvar Despesa' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const categories = ref([]);
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const maxDate = tomorrow.toISOString().split('T')[0];

const getInitialForm = () => ({
    description: '',
    amount: '',
    date: new Date().toISOString().split('T')[0],
    category_id: ''
});

const form = ref(getInitialForm());

const getValidationMessage = (errors) => {
    const firstField = Object.keys(errors || {})[0];
    if (!firstField) return 'Dados inválidos. Verifique os campos.';

    const fieldMessages = errors[firstField];
    if (!fieldMessages || fieldMessages.length === 0) return 'Dados inválidos. Verifique os campos.';

    return fieldMessages[0];
};

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data;
    } catch (error) {
        errorMessage.value = 'Erro ao buscar categorias.';
    }
};

const saveExpense = async () => {
    loading.value = true;
    successMessage.value = '';
    errorMessage.value = '';

    try {
        await axios.post('/api/expenses', form.value);
        form.value = getInitialForm();
        successMessage.value = 'Despesa salva com sucesso!';

        setTimeout(() => {
            router.push('/despesas');
        }, 500);
    } catch (error) {
        if (error.response?.status === 422) {
            errorMessage.value = getValidationMessage(error.response.data.errors);
        } else {
            errorMessage.value = 'Erro ao salvar despesa. Tente novamente.';
        }
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchCategories();
});
</script>

<style scoped>
.page-wrapper {
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

.nav-link {
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.nav-link:hover {
    color: #0f172a;
}

.content {
    padding: 2rem 5%;
    max-width: 600px;
    margin: 0 auto;
}

.header-section {
    margin-bottom: 1rem;
}

.page-title {
    color: #0f172a;
    margin: 0;
}

.success-message {
    background-color: #f0fdf4;
    color: #166534;
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    border: 1px solid #bbf7d0;
    font-weight: 500;
}

.error-message {
    background-color: #fef2f2;
    color: #b91c1c;
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    border: 1px solid #fecaca;
    font-weight: 500;
}

.card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #334155;
    font-weight: 600;
}

input, select {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: all 0.2s;
    background-color: white;
}

input:focus, select:focus {
    outline: none;
    border-color: #0f172a;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    align-items: center;
}

.btn-cancel {
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}

.btn-cancel:hover {
    color: #0f172a;
}

.btn-submit {
    background-color: #0f172a;
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover:not(:disabled) {
    background-color: #ea580c;
}

.btn-submit:disabled {
    background-color: #94a3b8;
    cursor: not-allowed;
}
</style>
