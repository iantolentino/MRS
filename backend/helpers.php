<?php
declare(strict_types=1);

function e(mixed $s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function json_out(array $v, int $code=200): never { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); echo json_encode($v, JSON_UNESCAPED_UNICODE|JSON_INVALID_UTF8_SUBSTITUTE); exit; }
function user(): ?array { if (!isset($_SESSION['user']['id'])) return null; $q=db()->prepare('SELECT id,name,email,role,department FROM users WHERE id=? AND active=1'); $q->execute([$_SESSION['user']['id']]); $fresh=$q->fetch(); if (!$fresh) { unset($_SESSION['user']); return null; } $_SESSION['user']=$fresh; return $fresh; }
function require_user(): array { if (!user()) json_out(['error'=>'Please sign in.'],401); return user(); }
function require_super(): array { $u=require_user(); if ($u['role']!=='super_admin') json_out(['error'=>'Super Admin access required.'],403); return $u; }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { $token=$_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_csrf'] ?? ''); if (!hash_equals(csrf(), (string)$token)) json_out(['error'=>'Security token expired. Refresh and try again.'],419); }
function bump(): int { $p=db(); $p->exec('UPDATE app_state SET revision=revision+1 WHERE id=1'); return (int)$p->query('SELECT revision FROM app_state WHERE id=1')->fetchColumn(); }
function valid_datetime(string $s): ?DateTimeImmutable { $d=DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $s); return $d ?: null; }
function conflict(int $room, string $start, string $end, ?int $ignore=null): bool { $sql="SELECT COUNT(*) FROM reservations WHERE room_id=? AND status IN ('pending','approved') AND start_at < ? AND end_at > ?"; $args=[$room,$end,$start]; if ($ignore!==null){$sql.=' AND id<>?';$args[]=$ignore;} $q=db()->prepare($sql);$q->execute($args);return (int)$q->fetchColumn()>0; }
function public_rows(string $from, string $to): array { $q=db()->prepare("SELECT r.id,r.room_id,r.department,r.start_at,r.end_at,r.status,rm.name room_name FROM reservations r JOIN rooms rm ON rm.id=r.room_id WHERE r.status IN ('pending','approved') AND r.start_at < ? AND r.end_at > ? ORDER BY r.start_at");$q->execute([$to,$from]);return $q->fetchAll(); }
