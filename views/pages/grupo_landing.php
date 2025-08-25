<!-- Informações do Grupo -->
<div class="text-center mb-8">
    <?php if (!empty($grupo['logo_url'])): ?>
        <img src="<?= htmlspecialchars($grupo['logo_url']) ?>" alt="Logo de <?= htmlspecialchars($grupo['nome']) ?>" class="mx-auto h-24 w-auto">
    <?php endif; ?>
    <h1 class="text-4xl font-bold mt-4"><?= htmlspecialchars($grupo['nome']) ?></h1>
    <p class="text-lg text-gray-600 mt-2"><?= htmlspecialchars($grupo['descricao']) ?></p>
</div>

<!-- Adicione um campo oculto para o JS saber qual é o slug do grupo -->
<input type="hidden" id="grupo-slug" value="<?= htmlspecialchars($grupo['slug']) ?>">