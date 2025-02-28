# GeekMusclay Template - Lightweight templating
The old way : 
```php
include __DIR__ . '/vendor/autoload.php';

use Geekmusclay\Template\Core\Template;

$patterns = [
	'/{{(.+)}}/' => '<?= $1; ?>',
	'/@if \((.+)\)/' => '<?php if ($1): ?>',
	'/@elseif \(([\w\-\>]+)\)/' => '<?php else if ($1): ?>',
	'/@else/' => '<?php else: ?>',
	'/@endif/' => '<?php endif; ?>',
	'/@for \((.+) in (.+)\)/' => '<?php foreach ($2 as $1): ?>',
	'/@endfor/' => '<?php endforeach; ?>',
	'/@php/' => '<?php',
	'/@endphp/' => '?>',
];

$template = new Template(__DIR__ . '/templates', $patterns, __DIR__ . '/cache');

echo $template->processOrGetFromCache('index.php');
```
The new way : 
```php
include __DIR__ . '/vendor/autoload.php';

use Geekmusclay\Template\Core\Processor;

$patterns = [
	'/{{(.+)}}/' => '<?= $1; ?>',
	'/@if \((.+)\)/' => '<?php if ($1): ?>',
	'/@elseif \(([\w\-\>]+)\)/' => '<?php else if ($1): ?>',
	'/@else/' => '<?php else: ?>',
	'/@endif/' => '<?php endif; ?>',
	'/@for \((.+) in (.+)\)/' => '<?php foreach ($2 as $1): ?>',
	'/@endfor/' => '<?php endforeach; ?>',
	'/@php/' => '<?php',
	'/@endphp/' => '?>',
];

try {
	$processor = new Processor(
		__DIR__ . '/templates',
		$patterns,
		__DIR__ . '/cache'
	);

	echo $processor->render('index.php');
} catch (\Throwable $e) {
	echo $e->getMessage();
}
```
In an `templates/index.php` file : 
```html
@php
class User
{
    public function isOk(): bool
    {
        return true;
    }
}

$user = new User();
@endphp

@if (true === $user->isOk())
    <p>C\'est ok</p>
@else
    <p>C\'est pas ok</p>
@endif

<ul>
    @for ($key => $item in $items)
        <li>{{ $item }}</li>
    @endfor
</ul>
```
