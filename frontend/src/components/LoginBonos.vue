<template>
    <div class="form-container">
      <h2 ref="title" class="form-title">Formulario de Bonos</h2>
  
      <form @submit.prevent="enviarFormulario" class="bonos-form">
        <div class="form-group" v-for="(field, index) in fields" :key="index">
          <label :for="field.id">{{ field.label }}</label>
          <input
            v-model="form[field.id]"
            :id="field.id"
            :type="field.type"
            :placeholder="field.placeholder"
            required
          />
        </div>
  
        <button type="submit" ref="btn" class="btn-enviar">Enviar</button>
      </form>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue'
  import { gsap } from 'gsap'
  
  // Campos del formulario
  const fields = [
    { id: 'nombre', label: 'Nombre', type: 'text', placeholder: 'Ingresa tu nombre' },
    { id: 'email', label: 'Correo', type: 'email', placeholder: 'correo@ejemplo.com' },
    { id: 'monto', label: 'Monto del bono', type: 'number', placeholder: 'Ej: 100000' },
  ]
  
  const form = reactive({
    nombre: '',
    email: '',
    monto: ''
  })
  
  const title = ref(null)
  const btn = ref(null)
  
  onMounted(() => {
    // Animación con GSAP
    gsap.from(title.value, { y: -50, opacity: 0, duration: 1 })
    gsap.from('.form-group', { x: -30, opacity: 0, stagger: 0.2, duration: 0.8 })
    gsap.from(btn.value, { scale: 0, opacity: 0, duration: 0.6, delay: 1 })
  })
  
  function enviarFormulario() {
    console.log('Formulario enviado:', { ...form })
    alert('Formulario enviado correctamente')
  }
  </script>
  
  <style lang="scss" scoped>
  $form-color: #6200ea;
  $bg-light: #f4f3ff;
  
  .form-container {
    max-width: 500px;
    margin: 3rem auto;
    background: white;
    padding: 2rem;
    border-radius: 1.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  
    .form-title {
      text-align: center;
      color: $form-color;
      margin-bottom: 1.5rem;
    }
  
    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 1rem;
  
      label {
        font-weight: 600;
        margin-bottom: 0.3rem;
      }
  
      input {
        padding: 0.6rem;
        border: 1px solid #ccc;
        border-radius: 0.5rem;
        transition: border-color 0.3s;
  
        &:focus {
          border-color: $form-color;
          outline: none;
        }
      }
    }
  
    .btn-enviar {
      background: $form-color;
      color: white;
      padding: 0.8rem;
      border: none;
      border-radius: 0.5rem;
      width: 100%;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s;
  
      &:hover {
        background: darken($form-color, 10%);
        transform: scale(1.02);
      }
    }
  }
  </style>
  