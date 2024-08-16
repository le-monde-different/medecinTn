<?php

namespace App\Test\Controller;

use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class QuestionControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/question/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Question::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Question index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'question[titre]' => 'Testing',
            'question[contenu]' => 'Testing',
            'question[dateCreation]' => 'Testing',
            'question[Utilisateur]' => 'Testing',
            'question[categorie]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Question();
        $fixture->setTitre('My Title');
        $fixture->setContenu('My Title');
        $fixture->setDateCreation('My Title');
        $fixture->setUtilisateur('My Title');
        $fixture->setCategorie('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Question');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Question();
        $fixture->setTitre('Value');
        $fixture->setContenu('Value');
        $fixture->setDateCreation('Value');
        $fixture->setUtilisateur('Value');
        $fixture->setCategorie('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'question[titre]' => 'Something New',
            'question[contenu]' => 'Something New',
            'question[dateCreation]' => 'Something New',
            'question[Utilisateur]' => 'Something New',
            'question[categorie]' => 'Something New',
        ]);

        self::assertResponseRedirects('/question/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getContenu());
        self::assertSame('Something New', $fixture[0]->getDateCreation());
        self::assertSame('Something New', $fixture[0]->getUtilisateur());
        self::assertSame('Something New', $fixture[0]->getCategorie());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Question();
        $fixture->setTitre('Value');
        $fixture->setContenu('Value');
        $fixture->setDateCreation('Value');
        $fixture->setUtilisateur('Value');
        $fixture->setCategorie('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/question/');
        self::assertSame(0, $this->repository->count([]));
    }
}
