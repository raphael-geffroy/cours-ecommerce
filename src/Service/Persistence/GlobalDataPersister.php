<?php

namespace App\Service\Persistence;

use Doctrine\ORM\EntityManagerInterface;
use App\DataPersister\PurchasePersister;

final class GlobalDataPersister
{
    /** @var array<DataPersisterInterface> */
    public $persisters;
    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(
        EntityManagerInterface $em,
        PurchasePersister $purchasePersister
    ) {
        $this->em = $em;
        $this->persisters = [
            $purchasePersister
        ];
    }

    public function supports($data, array $context = []): bool
    {
        foreach ($this->persisters as $persister) {
            if ($persister->supports($data, $context)) {
                return true;
            }
        }
        return false;
    }

    public function persist($data, array $context = [])
    {
        foreach ($this->persisters as $persister) {
            if ($persister->supports($data, $context)) {
                $data = $persister->persist($data, $context) ?? $data;
                return $data;
            }
        }

        $this->em->persist($data);
        $this->em->flush();
        return $data;
    }

    public function remove($data, array $context = [])
    {
        foreach ($this->persisters as $persister) {
            if ($persister->supports($data, $context)) {
                $persister->remove($data, $context);
                return;
            }
        }

        $this->em->remove($data);
        $this->em->flush();
    }

    public function flush()
    {
        $this->em->flush();
    }
}
