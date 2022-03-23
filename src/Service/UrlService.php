<?php
namespace App\Service;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UrlRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;

class UrlService  
{
    private EntityManagerInterface $em;
    private Security $security;
    private UrlRepository $urlRepo;
    public function __construct(EntityManagerInterface $em, Security $security, UrlRepository $urlRepo)
    {
        $this->em = $em;
        $this->security = $security;
        $this->urlRepo = $urlRepo;
       

    }

    public function addUrl( string $longUrl , string $domain) : Url
    {
        $url = new Url();

        $user=$this->security->getUser();


        $hash=$this->generateHash();
        $link = $_SERVER['HTTP_ORIGIN'] . "/$hash";

        $url->setDomain($domain)->setUser($user)->setLongUrl($longUrl) ->setHash($hash)->setLink($link)->setCreatedAt(new \DateTime);

        $this->em->persist($url);
        $this->em->flush();
        return $url;
    }

    public function parseUrl(string $url) : string|bool
    {
        $domain = parse_url($url ,PHP_URL_HOST);
        if (!$domain) {
            return false;
        }
        if (!filter_var(gethostbyname($domain),FILTER_VALIDATE_IP)) {
            return false;
        }
        return $domain;
    }
    public function generateHash(int $offset = 0, int $length = 8): string
    {
        return substr(md5(uniqid(mt_rand(), true)), $offset, $length);
    }
    public function deleteUrl(string $hash)
    {
        $url = $this->urlRepo->findOneBy(['hash' => $hash]);

        if (!$url) {
            return new JsonResponse([
                'statusCode' => 'URL_NOT_FOUND',
                'statusText' => "Le lien n'a pas été trouvé !"
            ]);
        }

        $this->em->remove($url);
        $this->em->flush();

        return new JsonResponse([
            'statusCode' => 'DELETE_SUCCESSFUL',
            'statusText' => 'Le lien a bien été supprimé !'
        ]);
    }
}
