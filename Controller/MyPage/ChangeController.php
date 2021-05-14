<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Summary\Controller\Mypage;

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Front\EntryType;
use Eccube\Repository\CustomerRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Plugin\Summary\Repository\ConfigRepository;

class ChangeController extends \Eccube\Controller\AbstractController
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var ConfigRepository
     */
    protected $ConfigRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        EncoderFactoryInterface $encoderFactory,
        TokenStorageInterface $tokenStorage,
        ConfigRepository $ConfigRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->encoderFactory = $encoderFactory;
        $this->tokenStorage = $tokenStorage;
        $this->ConfigRepository = $ConfigRepository;
    }

    /**
     * 会員情報編集画面.
     *
     * @Route("/mypage/change", name="mypage_change")
     * @Template("Summary\Resource\default\Mypage/change.twig")
     */
    public function index(Request $request)
    {
        $Customer = $this->getUser();
        $LoginCustomer = clone $Customer;
        $this->entityManager->detach($LoginCustomer);

        $previous_password = $Customer->getPassword();
        $Customer->setPassword($this->eccubeConfig['eccube_default_password']);

        /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
        $builder = $this->formFactory->createBuilder(EntryType::class, $Customer);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE, $event);

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();
        $form->handleRequest($request);

        #追加したソース
        #$ConfigRepositoryを追加すると500エラーが出る
        #getの使い方が違う？二要素認証のソースを参考に適当に値を入れてる
        #$Summary = $request->get('Summary');
        $requestSummary = $request->get('Summary');
        $Summary = $this->ConfigRepository->get($Customer);
        #$Summary = $this->ConfigRepository->get();
        #$Summary = $ConfigRepository->getSummaryColumn();

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('会員編集開始');

            if ($Customer->getPassword() === $this->eccubeConfig['eccube_default_password']) {
                $Customer->setPassword($previous_password);
            } else {
                $encoder = $this->encoderFactory->getEncoder($Customer);
                if ($Customer->getSalt() === null) {
                    $Customer->setSalt($encoder->createSalt());
                }
                $Customer->setPassword(
                    $encoder->encodePassword($Customer->getPassword(), $Customer->getSalt())
                );
            }
            $this->entityManager->flush();
            #$CustomInfo = $this->ConfigRepository->newSummaryConfig();
            $CustomInfo = $this->ConfigRepository->get($Customer);
            $CustomInfo->setSummaryColumn($requestSummary);
            $this->entityManager->persist($CustomInfo);
            $this->entityManager->flush();
            log_info('会員編集完了');

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Customer' => $Customer,  
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE, $event);

            return $this->redirect($this->generateUrl('mypage_change_complete'));
        }

        $this->tokenStorage->getToken()->setUser($LoginCustomer);

        return [
            'form' => $form->createView(),
            #追加したソース
            #追記→ここのreturnで返すことで画面に反映されるっぽい
            'Summary' => $Summary,
        ];
    }

    /**
     * 会員情報編集完了画面.
     *
     * @Route("/mypage/change_complete", name="mypage_change_complete")
     * @Template("Mypage/change_complete.twig")
     */
    public function complete(Request $request)
    {
        return [];
    }
}
