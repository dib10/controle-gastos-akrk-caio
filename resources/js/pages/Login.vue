<template>
    <div class="login-wrapper">
        <div class="login-card">
            <h2 class="title">AKRK Finanças</h2>
            <p class="subtitle">Faça login para gerenciar seus gastos</p>

            <div v-if="successMessage" class="success-message">
                {{ successMessage }}
            </div>

            <div v-if="errorMessage" class="error-message">
                {{ errorMessage }}
            </div>

            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" v-model="form.email" required placeholder="Seu e-mail" />
                </div>

                <div class="form-group">
                    <label>Senha</label>
                    <input type="password" v-model="form.password" required placeholder="Sua senha" />
                </div>

                <button type="submit" :disabled="loading" class="btn-submit">
                    {{ loading ? 'Entrando...' : 'Entrar' }}
                </button>
            </form>

            <p class="register-link">
                Não tem conta?
                <router-link to="/register">Criar cadastro</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();

const form = ref({
    email: 'caiodib10@gmail.com',
    password: 'caio'
});

const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const getValidationMessage = (errors) => {
    const firstField = Object.keys(errors || {})[0];
    if (!firstField) return 'Dados inválidos. Verifique os campos.';

    const fieldMessages = errors[firstField];
    if (!fieldMessages || fieldMessages.length === 0) return 'Dados inválidos. Verifique os campos.';

    return fieldMessages[0];
};

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await axios.post('/api/login', form.value);

        localStorage.setItem('token', response.data.access_token);
        successMessage.value = 'Login efetuado com sucesso!';

        setTimeout(() => {
            router.push('/dashboard');
        }, 500);
    } catch (error) {
        if (error.response?.status === 401) {
            errorMessage.value = 'E-mail ou senha incorretos.';
        } else if (error.response?.status === 422) {
            errorMessage.value = getValidationMessage(error.response.data.errors);
        } else {
            errorMessage.value = 'O erro foi do nosso lado! Tente mais tarde.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
}

.login-card {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    width: 100%;
    max-width: 400px;
}

.title {
    color: #0f172a;
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    text-align: center;
    letter-spacing: -0.5px;
}

.subtitle {
    color: #64748b;
    font-size: 0.95rem;
    text-align: center;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.2rem;
}

label {
    display: block;
    margin-bottom: 0.4rem;
    font-size: 0.9rem;
    color: #334155;
    font-weight: 600;
}

input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: all 0.2s;
}

input:focus {
    outline: none;
    border-color: #0f172a;
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
}

.success-message {
    background-color: #f0fdf4;
    color: #166534;
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    text-align: center;
    border: 1px solid #bbf7d0;
    font-weight: 500;
}

.error-message {
    background-color: #fef2f2;
    color: #b91c1c;
    padding: 0.75rem;
    border-radius: 6px;
    margin-bottom: 1rem;
    font-size: 0.85rem;
    text-align: center;
    border: 1px solid #fecaca;
    font-weight: 500;
}

.btn-submit {
    width: 100%;
    padding: 0.85rem;
    background-color: #0f172a;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 1rem;
}

.btn-submit:hover:not(:disabled) {
    background-color: #ea580c;
    transform: translateY(-1px);
}

.btn-submit:disabled {
    background-color: #94a3b8;
    cursor: not-allowed;
    transform: none;
}

.register-link {
    margin-top: 1rem;
    text-align: center;
    color: #64748b;
    font-size: 0.9rem;
}

.register-link a {
    color: #ea580c;
    font-weight: 600;
    text-decoration: none;
}

.register-link a:hover {
    color: #0f172a;
}
</style>
