<script setup>
import bgImage from '@/assets/background1.jpg'
import { ref } from 'vue'
import { UserIcon, LockIcon } from 'lucide-vue-next'
const user = ref('')
const password = ref('')
</script>

<template>
  <div class="flex w-screen h-screen">
    <div
      class="left_panel w-1/2 h-screen bg-cover bg-center rounded-tr-[25px] rounded-br-[25px] overflow-hidden shadow-[5px_0_20px_rgba(0,0,0,0.3)]"
      :style="{ backgroundImage: `url(${bgImage})` }"></div>

    <div class="center_panel w-[15px] bg-gray-150 shadow-inner"></div>

    <div
      class="right_panel w-1/2 flex flex-col items-center justify-center rounded-tl-[25px] rounded-bl-[25px] border-l border-l-[#74737326] bg-[#f3f6fb]">
      <div class="form_container flex flex-col items-center justify-center min-h-screen shadow-lg rounded-2xl p-8 w-full">
        <h1 class="title_right_panel text-[50px] font-montserrat font-black mb-8 text-gray-800 w-[50%] leading-tight">
          INVERSIONES DEL META MV
        </h1>

        <form action="#" method="post" class="flex flex-col h-[40%] w-[70%] justify-evenly">
          <div class="user mb-5">
            <label for="email" class="block text-gray-600 text-sm mb-2">Usuario</label>
            <input type="text" id="user" name="user" placeholder="Usuario" required
              class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 text-gray-700" />
          </div>

          <div class="password mb-5">
            <label for="password" class="block text-gray-600 text-sm mb-2">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required
              class="w-full px-4 py-3 border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 text-gray-700" />
          </div>

          <div class="remember_container flex items-center justify-between text-sm text-gray-600 mb-6">
            <label class="remember flex items-center space-x-2 w-[50%]">
              <input type="checkbox" class="w-4 h-4 text-indigo-500 rounded border-gray-300 focus:ring-indigo-400" />
              <span>Recordarme</span>
            </label>
            <a href="#" class="text-indigo-500 text-blue w-[50%]">Olvidaste la contraseña?</a>
          </div>

          <button type="submit"
            class="w-full bg-indigo-500 text-white font-semibold py-3 rounded-xl hover:bg-indigo-600 transition-all">
            Ingresar
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* =========================================================
   🌈 TRANSICIONES GLOBALES
   ========================================================= */
.left_panel,
.center_panel,
.right_panel,
h1,
form {
  transition: all 0.45s ease-in-out;
}

.user label,
.password label{
  font-weight: bold;
}

.user{
  padding-bottom: 5%;
}

.password{
  padding-bottom: 5%;
}

.remember input{
  width: 15%;
}

.remember_container a{
  text-align: right;
}

.remember span{
  width: 85%;
}

.remember_container{
  padding-bottom: 5%;
  justify-content: space-between;
}

.form_container h1 {
  text-align: center;
  font-size:35px;
  margin-bottom: 1.5rem !important;
}

/* =========================================================
   💻 DESKTOP GRANDE (>= 1200px)
   ========================================================= */
@media (max-width: 1200px) {
  .title_right_panel {
    font-size: 35px !important;
    width: 100%;
    text-align: center;
  }

  form {
    width: 75% !important;
  }
}

/* =========================================================
   🧱 LAPTOP (<= 992px)
   ========================================================= */
@media (max-width: 992px) {
  .left_panel {
    width: 45% !important;
  }

  .right_panel {
    width: 55% !important;
  }

  .right_panel h1 {
    font-size: 25px !important;
    text-align: center;
  }

  form {
    width: 80% !important;
  }
}

/* =========================================================
   📱 TABLET / MÓVIL GRANDE (<= 768px)
   ========================================================= */
@media (max-width: 768px) {
  /* Ocultar panel izquierdo y central */
  .left_panel,
  .center_panel {
    opacity: 0 !important;
    visibility: hidden !important;
    width: 0 !important;
    max-width: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    pointer-events: none !important;
    overflow: hidden !important;
  }

  /* Expandir el panel derecho a toda la pantalla */
  .right_panel {
    width: 100% !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    background-color: #f3f6fb !important;
  }

  /* Ajustes del título y formulario */
  .form_container h1 {
    font-size: 32px !important;
    width: 90% !important;
    text-align: center !important;
  }

  form {
    width: 90% !important;
    height: auto !important;
  }
}

/* =========================================================
   📲 CELULAR PEQUEÑO (<= 576px)
   ========================================================= */
@media (max-width: 576px) {
  .form_container h1 {
    font-size: 28px !important;
  }

  form {
    width: 95% !important;
  }

  button {
    font-size: 15px !important;
    padding: 12px !important;
  }
}

/* =========================================================
   📞 CELULAR EXTRA PEQUEÑO (<= 400px)
   ========================================================= */
@media (max-width: 400px) {

  .form_container{
    border-radius:0px;
    padding: 8%;
  }

  .form_container h1 {
    font-size: 24px !important;
    line-height: 1.2 !important;
  }

  form {
    width: 100% !important;
  }

  label {
    font-size: 13px !important;
  }

  input {
    font-size: 14px !important;
    padding: 10px !important;
  }

  button {
    font-size: 14px !important;
  }
}
</style>
