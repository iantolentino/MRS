<?php
declare(strict_types=1);

session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','samesite'=>'Lax','path'=>'/']);
session_start();
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'Asia/Manila');

function user(): ?array { if (!isset($_SESSION['user']['id'])) return null; $q=db()->prepare('SELECT id,name,email,role,department FROM users WHERE id=? AND active=1'); $q->execute([$_SESSION['user']['id']]); $fresh=$q->fetch(); if (!$fresh) { unset($_SESSION['user']); return null; } $_SESSION['user']=$fresh; return $fresh; }
function require_user(): array { if (!user()) json_out(['error'=>'Please sign in.'],401); return user(); }
function require_super(): array { $u=require_user(); if ($u['role']!=='super_admin') json_out(['error'=>'Super Admin access required.'],403); return $u; }
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function verify_csrf(): void { $token=$_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_csrf'] ?? ''); if (!hash_equals(csrf(), (string)$token)) json_out(['error'=>'Security token expired. Refresh and try again.'],419); }
