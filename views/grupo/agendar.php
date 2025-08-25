<?php /** @var array $group */ ?>
<h1>Agendar em <?= htmlspecialchars($group['nome']) ?></h1>

<form method="post" action="">
  <label>Profissional</label>
  <select name="profissional_id" required>
    <?php
      global $pdo; $st = $pdo->prepare('SELECT id,nome FROM profissionais WHERE grupo_id=? ORDER BY nome');
      $st->execute([$group['id']]);
      foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $p):
    ?>
      <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
    <?php endforeach; ?>
  </select>

  <label>Data/Hora</label>
  <input type="datetime-local" name="data_hora" required>

  <button type="submit">Confirmar</button>
</form>
