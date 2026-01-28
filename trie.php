<?php
class TrieNode {
    public function __construct(
        public array $children = [],
        public bool $isEndOfWord = false,
        public array $productIds = []
    ) {}
}

class Trie {
    private TrieNode $root;

    public function __construct() {
        $this->root = new TrieNode();
    }
    
    public function insert(string $word, int $productId): void {
        $current = $this->root;
        foreach (str_split(strtolower($word)) as $ch) {
            if (!isset($current->children[$ch])) {
                $current->children[$ch] = new TrieNode();
            }
            $current = $current->children[$ch];
            if (!in_array($productId, $current->productIds, true)) {
                $current->productIds[] = $productId;
            }
        }
        $current->isEndOfWord = true;
    }
    
    public function search(string $prefix): array {
        $current = $this->root;
        foreach (str_split(strtolower($prefix)) as $ch) {
            if (!isset($current->children[$ch])) {
                return [];
            }
            $current = $current->children[$ch];
        }
        return $current->productIds;
    }
}
?>