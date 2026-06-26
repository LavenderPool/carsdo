<?php

namespace App\Support\Articles;

class ArticleBodyRenderer
{
    public function render(?array $document): string
    {
        if (! $this->hasMeaningfulContent($document)) {
            return '';
        }

        return $this->renderNode($document);
    }

    public function toPlainText(?array $document): string
    {
        return trim(preg_replace('/\s+/u', ' ', $this->plainTextFromNode($document)) ?? '');
    }

    public function hasMeaningfulContent(?array $document): bool
    {
        return $this->toPlainText($document) !== '';
    }

    private function renderNode(mixed $node): string
    {
        if (! is_array($node)) {
            return '';
        }

        $type = (string) ($node['type'] ?? '');
        $content = $this->renderContent($node['content'] ?? null);

        return match ($type) {
            'doc' => $content,
            'paragraph' => trim($content) !== '' ? "<p>{$content}</p>" : '<p><br></p>',
            'heading' => $this->renderHeading($node, $content),
            'bulletList' => trim($content) !== '' ? "<ul>{$content}</ul>" : '',
            'orderedList' => trim($content) !== '' ? "<ol>{$content}</ol>" : '',
            'listItem' => "<li>{$content}</li>",
            'taskList' => trim($content) !== '' ? '<ul class="article-task-list">'.$content.'</ul>' : '',
            'taskItem' => $this->renderTaskItem($node, $content),
            'blockquote' => trim($content) !== '' ? "<blockquote>{$content}</blockquote>" : '',
            'horizontalRule' => '<hr>',
            'hardBreak' => '<br>',
            'text' => $this->renderTextNode($node),
            default => $content,
        };
    }

    private function renderContent(mixed $content): string
    {
        if (! is_array($content)) {
            return '';
        }

        $rendered = array_map(fn (mixed $node): string => $this->renderNode($node), $content);

        return implode('', array_filter($rendered, static fn (string $value): bool => $value !== ''));
    }

    private function renderHeading(array $node, string $content): string
    {
        $level = (int) data_get($node, 'attrs.level', 2);
        $level = max(2, min($level, 4));

        return trim($content) !== '' ? "<h{$level}>{$content}</h{$level}>" : '';
    }

    private function renderTaskItem(array $node, string $content): string
    {
        $checked = (bool) data_get($node, 'attrs.checked', false);
        $checkedAttribute = $checked ? ' checked' : '';
        $stateClass = $checked ? ' is-checked' : '';

        return '<li class="article-task-list__item'.$stateClass.'">'
            .'<label class="article-task-list__label">'
            .'<input type="checkbox" disabled'.$checkedAttribute.'>'
            .'<span>'.$content.'</span>'
            .'</label>'
            .'</li>';
    }

    private function renderTextNode(array $node): string
    {
        $text = htmlspecialchars((string) ($node['text'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $marks = $node['marks'] ?? [];

        if (! is_array($marks)) {
            return $text;
        }

        foreach ($marks as $mark) {
            if (! is_array($mark)) {
                continue;
            }

            $type = (string) ($mark['type'] ?? '');

            $text = match ($type) {
                'bold' => "<strong>{$text}</strong>",
                'italic' => "<em>{$text}</em>",
                'underline' => "<u>{$text}</u>",
                'strike' => "<s>{$text}</s>",
                'code' => "<code>{$text}</code>",
                'link' => $this->renderLink($text, $mark),
                default => $text,
            };
        }

        return $text;
    }

    private function renderLink(string $text, array $mark): string
    {
        $href = $this->sanitizeHref((string) data_get($mark, 'attrs.href', '#'));
        $target = (string) data_get($mark, 'attrs.target', '');
        $rel = trim((string) data_get($mark, 'attrs.rel', ''));
        $attributes = ' href="'.htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'"';

        if ($target !== '') {
            $attributes .= ' target="'.htmlspecialchars($target, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'"';
        }

        if ($rel !== '') {
            $attributes .= ' rel="'.htmlspecialchars($rel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'"';
        }

        return "<a{$attributes}>{$text}</a>";
    }

    private function sanitizeHref(string $href): string
    {
        $href = trim($href);

        if ($href === '') {
            return '#';
        }

        if (str_starts_with($href, '/') || str_starts_with($href, '#')) {
            return $href;
        }

        if (preg_match('/^(https?:|mailto:|tel:)/i', $href) === 1) {
            return $href;
        }

        return '#';
    }

    private function plainTextFromNode(mixed $node): string
    {
        if (! is_array($node)) {
            return '';
        }

        $type = (string) ($node['type'] ?? '');

        if ($type === 'text') {
            return (string) ($node['text'] ?? '');
        }

        if ($type === 'hardBreak') {
            return "\n";
        }

        $content = $node['content'] ?? null;
        $children = '';

        if (is_array($content)) {
            $children = implode('', array_map(fn (mixed $child): string => $this->plainTextFromNode($child), $content));
        }

        if (in_array($type, ['paragraph', 'heading', 'blockquote', 'listItem', 'taskItem'], true)) {
            return $children."\n";
        }

        if (in_array($type, ['bulletList', 'orderedList', 'taskList', 'doc'], true)) {
            return $children;
        }

        return $children;
    }
}
