<?php /** @var array $group */ /** @var array $profissionais */ ?>
<h1><?= htmlspecialchars($group['nome']) ?></h1>
<p><?= nl2br(htmlspecialchars($group['descricao'] ?? '')) ?></p>

<h2>Profissionais</h2>
<ul>
  <?php foreach ($profissionais as $p): ?>
    <li>
      <strong><?= htmlspecialchars($p['nome']) ?></strong>
      <?php if (!empty($p['especialidade'])): ?>
        — <?= htmlspecialchars($p['especialidade']) ?>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
</ul>

<a href="/g/<?= urlencode($group['slug']) ?>/agendar">Agendar agora</a>
