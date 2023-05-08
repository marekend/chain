<?php

class Block
{
    public int $index;
    public string $timestamp;
    public $data;
    public string $previousHash;
    public string $hash;

    public function __construct(int $index, string $timestamp, $data, string $previousHash = '')
    {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->previousHash = $previousHash;
        $this->hash = $this->calculateHash();
    }

    public function calculateHash(): string
    {
        return hash(
            'sha256', 
            sprintf(
               '%d%s%s%s',
               $this->index,
               $this->timestamp,
               $this->previousHash,
               json_encode($this->data),
           )
        );
    }
}


class Blockchain
{
    public array $chain;

    public function __construct()
    {
        $this->chain = [$this->createGenesisBlock()];
    }

    private function createGenesisBlock(): Block
    {
        return new Block(0, '01/05/2023', 'Genesis Block', '0');
    }

    public function getLatestBlock(): Block
    {
        return $this->chain[count($this->chain) - 1];
    }

    public function addBlock(Block $newBlock): void
    {
        $newBlock->previousHash = $this->getLatestBlock()->hash;
        $newBlock->hash = $newBlock->calculateHash();
        $this->chain[] = $newBlock;
    }

    public function isChainValid(): bool
    {
        for ($i = 1, $chainLength = count($this->chain); $i < $chainLength; $i++) 
        {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i - 1];

            if ($currentBlock->hash !== $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash !== $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }
}


$myBlockchain = new Blockchain();
$myBlockchain->addBlock(new Block(1, '06/05/2023', ['amount' => 50]));
$myBlockchain->addBlock(new Block(2, '07/05/2023', ['amount' => 100]));

function displayBlockchain(Blockchain $blockchain): void
{
    foreach ($blockchain->chain as $block) 
    {
        echo "Index: " . $block->index . "\n";
        echo "Timestamp: " . $block->timestamp . "\n";
        echo "Data: " . json_encode($block->data) . "\n";
        echo "Previous Hash: " . $block->previousHash . "\n";
        echo "Hash: " . $block->hash . "\n\n";
    }
}

displayBlockchain($myBlockchain);
echo 'Is blockchain valid? ' . ($myBlockchain->isChainValid() ? 'Yes' : 'No') . "\n";
