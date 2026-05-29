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
                <h1 class="page-title">Minhas Despesas</h1>
                <router-link to="/nova-despesa" class="btn-new">Nova Despesa</router-link>
            </div>

            <div v-if="successMessage" class="success-message">
                {{ successMessage }}
            </div>

            <div v-if="errorMessage" class="error-message">
                {{ errorMessage }}
            </div>

            <div v-if="loadingData" class="empty-state">
                Carregando despesas...
            </div>

            <div v-else-if="expenses.length === 0" class="empty-state">
                Você ainda não possui despesas cadastradas.
            </div>

            <div v-else class="expense-list">
                <div v-for="expense in expenses" :key="expense.id" class="expense-item">
                    <template v-if="editingExpenseId === expense.id">
                        <div class="edit-grid">
                            <input type="text" v-model="editForm.description" placeholder="Descrição" />
                            <input type="number" step="0.01" v-model="editForm.amount" placeholder="Valor" />
                            <input type="date" v-model="editForm.date" :max="maxDate" />
                            <select v-model="editForm.category_id">
                                <option value="" disabled>Selecione a categoria</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div class="item-actions">
                            <button @click="updateExpense(expense.id)" class="btn-edit" :disabled="loading">
                                Salvar
                            </button>
                            <button @click="cancelEdit" class="btn-cancel-inline" :disabled="loading">
                                Cancelar
                            </button>
                        </div>
                    </template>

                    <template v-else>
                        <div class="expense-info">
                            <strong>{{ expense.description }}</strong>
                            <span>{{ formatDate(expense.date) }} - {{ expense.category?.name || 'Sem categoria' }}</span>
                        </div>
                        <div class="expense-right">
                            <span class="expense-amount">{{ formatCurrency(expense.amount) }}</span>
                            <div class="item-actions">
                                <button @click="startEdit(expense)" class="btn-edit" :disabled="loading">
                                    Editar
                                </button>
                                <button @click="deleteExpense(expense.id)" class="btn-delete" :disabled="loading">
                                    Excluir
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const expenses = ref([]);
const categories = ref([]);

const loading = ref(false);
const loadingData = ref(true);
const successMessage = ref('');
const errorMessage = ref('');

const editingExpenseId = ref(null);
const editForm = ref({
    description: '',
    amount: '',
    date: '',
    category_id: ''
});

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const maxDate = tomorrow.toISOString().split('T')[0];

const clearMessages = () => {
    successMessage.value = '';
    errorMessage.value = '';
};

const getValidationMessage = (errors) => {
    const firstField = Object.keys(errors || {})[0];
    if (!firstField) return 'Dados inválidos. Verifique os campos.';

    const fieldMessages = errors[firstField];
    if (!fieldMessages || fieldMessages.length === 0) return 'Dados inválidos. Verifique os campos.';

    return fieldMessages[0];
};

const handleApiError = (error, fallbackMessage) => {
    if (error.response?.status === 422) {
        errorMessage.value = getValidationMessage(error.response.data.errors);
        return;
    }

    errorMessage.value = fallbackMessage;
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

const formatDate = (dateString) => {
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
};

const fetchData = async () => {
    try {
        const [expensesResponse, categoriesResponse] = await Promise.all([
            axios.get('/api/expenses'),
            axios.get('/api/categories')
        ]);

        expenses.value = expensesResponse.data;
        categories.value = categoriesResponse.data;
    } catch (error) {
        errorMessage.value = 'Erro ao buscar dados de despesas.';
    } finally {
        loadingData.value = false;
    }
};

const startEdit = (expense) => {
    clearMessages();
    editingExpenseId.value = expense.id;
    editForm.value = {
        description: expense.description,
        amount: expense.amount,
        date: expense.date,
        category_id: expense.category_id
    };
};

const cancelEdit = () => {
    editingExpenseId.value = null;
    editForm.value = {
        description: '',
        amount: '',
        date: '',
        category_id: ''
    };
};

const updateExpense = async (id) => {
    clearMessages();
    loading.value = true;

    try {
        await axios.put(`/api/expenses/${id}`, editForm.value);
        successMessage.value = 'Despesa atualizada com sucesso!';
        cancelEdit();
        await fetchData();
    } catch (error) {
        handleApiError(error, 'Erro ao atualizar despesa.');
    } finally {
        loading.value = false;
    }
};

const deleteExpense = async (id) => {
    if (!confirm('Tem certeza que quer excluir essa despesa?')) return;

    clearMessages();
    loading.value = true;

    try {
        await axios.delete(`/api/expenses/${id}`);
        successMessage.value = 'Despesa excluída com sucesso!';
        await fetchData();
    } catch (error) {
        handleApiError(error, 'Erro ao excluir despesa.');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
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
    max-width: 900px;
    margin: 0 auto;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.page-title {
    color: #0f172a;
    margin: 0;
}

.btn-new {
    background-color: #0f172a;
    color: white;
    text-decoration: none;
    padding: 0.65rem 1rem;
    border-radius: 6px;
    font-weight: 600;
}

.btn-new:hover {
    background-color: #ea580c;
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
    background: white;
    padding: 1rem 1.25rem;
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

.expense-right {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.8rem;
    gap: 1rem;
}

.expense-amount {
    font-weight: 700;
    color: #ea580c;
    font-size: 1.1rem;
}

.item-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-edit,
.btn-delete,
.btn-cancel-inline {
    border: 1px solid transparent;
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-edit {
    background: #e2e8f0;
    color: #0f172a;
}

.btn-edit:hover:not(:disabled) {
    background: #cbd5e1;
}

.btn-delete {
    background: transparent;
    color: #ef4444;
    border-color: #fecaca;
}

.btn-delete:hover:not(:disabled) {
    background: #fef2f2;
    border-color: #ef4444;
}

.btn-cancel-inline {
    background: transparent;
    color: #64748b;
    border-color: #cbd5e1;
}

.btn-cancel-inline:hover:not(:disabled) {
    background: #f8fafc;
}

.btn-edit:disabled,
.btn-delete:disabled,
.btn-cancel-inline:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.edit-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.6rem;
    margin-bottom: 0.8rem;
}

input,
select {
    width: 100%;
    padding: 0.65rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.95rem;
    box-sizing: border-box;
}

input:focus,
select:focus {
    outline: none;
    border-color: #0f172a;
}
</style>
