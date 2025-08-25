<?php /** @var array $group */ ?>
<h1>Painel — <?= htmlspecialchars($group['nome']) ?></h1>
<nav>
  <a href="/g/<?= urlencode($group['slug']) ?>/painel">Dashboard</a>
  <a href="/g/<?= urlencode($group['slug']) ?>/painel/agendamentos">Agendamentos</a>
  <a href="/g/<?= urlencode($group['slug']) ?>/painel/profissionais">Profissionais</a>
</nav>
<p>Conteúdos do painel visíveis apenas a membros deste grupo.</p>
