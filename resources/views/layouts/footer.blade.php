<div class="bottom-0 left-0 w-full z-30 pointer-events-none">
  <footer class="bg-emerald-800 rounded-base shadow-xs border border-default m-4 text-white">
    <div class="w-full mx-auto max-w-screen-xl p-4 flex justify-center items-center">
      <span class="text-sm sm:text-center">
        © <span id="year"></span> <a href="https://flowbite.com/" class="hover:underline">Hadi Santoso</a>. All Rights Reserved.
      </span>
    </div>
  </footer>
</div>

<script>
  document.getElementById('year').textContent = new Date().getFullYear();
</script>
