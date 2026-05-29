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
                <h1 class="page-title">Gerenciar Categorias</h1>
            </div>

            <div v-if="successMessage" class="success-message">
                {{ successMessage }}
            </div>

            <div v-if="errorMessage" class="error-message">
                {{ errorMessage }}
            </div>

            <div class="card form-card">
                <h3>Nova Categoria</h3>
                <form @submit.prevent="addCategory" class="add-form">
                    <input
                        type="text"
                        v-model="newCategoryName"
                        placeholder="Ex: Alimentação, Transporte, Lazer..."
                        required
                        :disabled="loading"
                    />
                    <button type="submit" class="btn-submit" :disabled="loading || !newCategoryName">
                        {{ loading ? 'Carregando...' : 'Adicionar' }}
                    </button>
                </form>
            </div>

            <div class="list-section">
                <h3>As Minhas Categorias</h3>

                <div v-if="loadingData" class="empty-state">
                    Carregando categorias...
                </div>

                <div v-else-if="categories.length === 0" class="empty-state">
                    Ainda não há categorias. Crie a primeira!
                </div>

                <div v-else class="category-list">
                    <div v-for="category in categories" :key="category.id" class="category-item">
                        <template v-if="editingCategoryId === category.id">
                            <input
                                type="text"
                                v-model="editingCategoryName"
                                class="edit-input"
                                :disabled="loading"
                            />
                            <div class="item-actions">
                                <button
                                    @click="updateCategory(category.id)"
                                    class="btn-edit"
                                    :disabled="loading || !editingCategoryName"
                                >
                                    Salvar
                                </button>
                                <button
                                    @click="cancelEdit"
                                    class="btn-cancel-inline"
                                    :disabled="loading"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </template>

                        <template v-else>
                            <span class="category-name">{{ category.name }}</span>
                            <div class="item-actions">
                                <button
                                    @click="startEdit(category)"
                                    class="btn-edit"
                                    :disabled="loading"
                                >
                                    Editar
                                </button>
                                <button
                                    @click="deleteCategory(category.id)"
                                    class="btn-delete"
                                    :disabled="loading"
                                >
                                    Excluir
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const categories = ref([]);
const newCategoryName = ref('');
const editingCategoryId = ref(null);
const editingCategoryName = ref('');

const loading = ref(false);
const loadingData = ref(true);
const errorMessage = ref('');
const successMessage = ref('');

const clearMessages = () => {
    errorMessage.value = '';
    successMessage.value = '';
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

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        categories.value = response.data;
    } catch (error) {
        errorMessage.value = 'Erro ao buscar categorias.';
    } finally {
        loadingData.value = false;
    }
};

const addCategory = async () => {
    clearMessages();
    loading.value = true;

    try {
        await axios.post('/api/categories', { name: newCategoryName.value });
        newCategoryName.value = '';
        successMessage.value = 'Categoria criada com sucesso!';
        await fetchCategories();
    } catch (error) {
        handleApiError(error, 'Erro ao criar categoria.');
    } finally {
        loading.value = false;
    }
};

const startEdit = (category) => {
    clearMessages();
    editingCategoryId.value = category.id;
    editingCategoryName.value = category.name;
};

const cancelEdit = () => {
    editingCategoryId.value = null;
    editingCategoryName.value = '';
};

const updateCategory = async (id) => {
    clearMessages();
    loading.value = true;

    try {
        await axios.put(`/api/categories/${id}`, { name: editingCategoryName.value });
        successMessage.value = 'Categoria atualizada com sucesso!';
        cancelEdit();
        await fetchCategories();
    } catch (error) {
        handleApiError(error, 'Erro ao atualizar categoria.');
    } finally {
        loading.value = false;
    }
};

const deleteCategory = async (id) => {
    if (!confirm('Tem certeza que quer apagar essa categoria?')) return;

    clearMessages();
    loading.value = true;

    try {
        await axios.delete(`/api/categories/${id}`);
        successMessage.value = 'Categoria excluída com sucesso!';
        await fetchCategories();
    } catch (error) {
        handleApiError(error, 'Erro ao apagar categoria.');
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
    max-width: 800px;
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
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
}

.card h3 {
    color: #0f172a;
    margin-top: 0;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.add-form {
    display: flex;
    gap: 1rem;
}

input {
    flex: 1;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 1rem;
    transition: all 0.2s;
}

input:focus {
    outline: none;
    border-color: #0f172a;
}

.btn-submit {
    background-color: #0f172a;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
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

.category-list {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.category-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    gap: 1rem;
}

.category-name {
    font-weight: 600;
    color: #0f172a;
}

.edit-input {
    margin: 0;
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
</style>
