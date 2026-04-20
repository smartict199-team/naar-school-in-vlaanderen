<?php

declare(strict_types=1);

namespace App\Security;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwner\GenericOAuth2ResourceOwner;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Auth0ResourceOwner extends GenericOAuth2ResourceOwner
{
    /**
     * @var array<array-key, mixed>
     */
    protected $paths = [
        'identifier' => 'sub',
        'nickname' => 'sub',
        'realname' => 'name',
        'email' => 'email',
        'profilepicture' => 'picture',
        'idToken.https://naarschoolinvlaanderen.be/roles' => 'roles',
    ];

    /**
     * @param string $redirectUri
     * @param array<array-key, mixed> $extraParameters
     */
    public function getAuthorizationUrl($redirectUri, array $extraParameters = []): string
    {
        return parent::getAuthorizationUrl($redirectUri, array_merge([
            'audience' => $this->options['audience'],
        ], $extraParameters));
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'authorization_url' => '{base_url}/authorize',
            'access_token_url' => '{base_url}/oauth/token',
            'infos_url' => '{base_url}/userinfo',
            'audience' => '{base_url}/userinfo',
        ]);

        $resolver->setRequired([
            'base_url',
        ]);

        $normalizer = function (Options $options, $value): string {
            return str_replace('{base_url}', $options['base_url'], $value);
        };

        $resolver->setNormalizer('authorization_url', $normalizer);
        $resolver->setNormalizer('access_token_url', $normalizer);
        $resolver->setNormalizer('infos_url', $normalizer);
        $resolver->setNormalizer('audience', $normalizer);
    }
}
