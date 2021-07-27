<?php

namespace App\Repository;

use App\Entity\AuthLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AuthLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthLog[]    findAll()
 * @method AuthLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthLog::class);
    }

    public const BLACK_LISTING_DELAY_IN_MINUTES = 10;

    public const MAX_FAILED_AUTH_ATTEMPTS = 5;

    /**
     * Adds a failed authentication attempt
     *
     * @param string $emailEntered
     * @param string|null $userIP
     * @param bool $isBlackListed Set to true if the email/userIP pair must ne blacklisted.
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addFailedAuthAttempt(
        string $emailEntered,
        ?string $userIP,
        bool $isBlackListed = false
    ): void
    {
        $authAttempt = (new AuthLog($emailEntered, $userIP))->setIsSuccessfulAuth(false);

        if($isBlackListed){
            $authAttempt->setStartOfBlackListing(new \DateTimeImmutable('now'))
                        ->setEndOfBlackListing(new \DateTimeImmutable(sprintf('+%d minutes', self::BLACK_LISTING_DELAY_IN_MINUTES)));
        }

        $this->_em->persist($authAttempt);

        $this->_em->flush();
    }

    /**
     * Adds a successful authentication attempt
     *
     * @param string $emailEntered
     * @param string|null $userIP
     * @param bool $isRememberMeAuth Set to true if the user is authenticated by remember cookie
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addSuccessfulAuthAttempt(
        string $emailEntered,
        ?string $userIP,
        bool $isRememberMeAuth = false
    ): void
    {
        $authAttempt = new AuthLog($emailEntered, $userIP);

        $authAttempt->setIsSuccessfulAuth(true)
                    ->setIsRememberMeAuth($isRememberMeAuth);

        $this->_em->persist($authAttempt);

        $this->_em->flush();
    }

    /**
     *
     * Returns the number of recent failed authentication attempts.
     *
     * @param string $emailEntered
     * @param string|null $userIP
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getRecentAuthAttemptFailure(
        string $emailEntered,
        ?string $userIP
    ): int
    {
        return $this->createQueryBuilder('af')
                    ->select('COUNT(af)')
                    ->where('af.authAttemptAt >= :datetime')
                    ->andWhere('af.userIP = :userIP')
                    ->andWhere('af.emailEntered = :email_entered')
                    ->setParameters([
                        'datetime'      => new \DateTimeImmutable(sprintf('-%d minutes', self::BLACK_LISTING_DELAY_IN_MINUTES)),
                        'email_entered' => $emailEntered,
                        'userIP'        => $userIP
                    ])
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    /**
     * Returns whether or not the user will be blacklisted on the next failed attempt.
     *
     * @param string $emailEntered
     * @param string|null $userIP
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isBlackListedWithThisAttemptFailure(
        string $emailEntered,
        ?string $userIP
    ): bool
    {
        return $this->getRecentAuthAttemptFailure($emailEntered, $userIP) >= self::MAX_FAILED_AUTH_ATTEMPTS -2;
    }

    /**
     * Returns the last entry in the blacklist of an email/userIP pair if it exists.
     *
     * @param string $emailEntered
     * @param string|null $userIP
     * @return AuthLog|null
     * @throws NonUniqueResultException
     */
    public function getEmailAndUserIpPairBlackListedIfExists(
        string $emailEntered,
        ?string $userIP
    ): ?AuthLog
    {
        return $this->createQueryBuilder('bl')
                    ->select('bl')
                    ->where('bl.userIP = :user_IP')
                    ->andWhere('bl.emailEntered = :email_entered')
                    ->andWhere('bl.endOfBlackListing IS NOT NULL')
                    ->andWhere('bl.endOfBlackListing >= :datetime')
                    ->setParameters([
                        'datetime'          => new \DateTimeImmutable(sprintf('-%d minutes', self::BLACK_LISTING_DELAY_IN_MINUTES)),
                        'email_entered'     => $emailEntered,
                        'user_IP'           => $userIP
                    ])
                    ->orderBy('bl.id', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    /**
     * Returns the end of black-listing rounded up to the next minute.
     *
     * @param string $emailEntered
     * @param string|null $userIP
     * @return string|null
     * @throws NonUniqueResultException
     */
    public function getEndOfBlackListing(
        string $emailEntered,
        ?string $userIP
    ): ?string
    {
        $blackListing = $this->getEmailAndUserIpPairBlackListedIfExists($emailEntered, $userIP);

        if(!$blackListing || $blackListing->getEndOfBlackListing() === null){
            return null;
        }

        return $blackListing->getEndOfBlackListing()->add(new \DateInterval("PT1M"))->format('H\hi');
    }
}
