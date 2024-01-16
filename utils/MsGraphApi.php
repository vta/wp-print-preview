<?php

namespace wp_print_preview\utils;

use Microsoft\Graph\Core\Authentication\GraphPhpLeagueAuthenticationProvider;
use Microsoft\Graph\Generated\Models\User;
use Microsoft\Graph\Generated\Users\Item\UserItemRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Users\UsersRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Models\OdataErrors\OdataError;
use Microsoft\Graph\Generated\Models\UserCollectionResponse;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;

/**
 * Microsoft Graph API for our VTA WP needs.
 */
class MsGraphApi {

	const SELECT_PARAMS = [
		'displayName',
		'givenName',
		'surname',
		'jobTitle',
		'department',
		'mail',
		'userPrincipalName',
		'businessPhones',
		'mobilePhone',
	];

	private GraphServiceClient $_graphClient;

	/**
	 * Initialize the Graph API client.
	 */
	public function __construct() {
		$this->_graphClient = $this->_initClient();
	}

	/**
	 * Searches a user by UPN. Will log error and return null if the user is not found.
	 * @param string $upn (ex. "pham_j@vta.org")
	 * @return User | null
	 */
	public function search_user_by_upn( string $upn ): ?User {
		try {
			// remove white spaces
			$email = trim($upn);

			$queryParams         = UserItemRequestBuilderGetRequestConfiguration::createQueryParameters();
			$queryParams->select = $this::SELECT_PARAMS;

			$requestConfig                  = new UserItemRequestBuilderGetRequestConfiguration();
			$requestConfig->queryParameters = $queryParams;

			return $this->_graphClient->users()->byUserId($email)->get($requestConfig)->wait();

		} catch ( OdataError $e ) {
			error_log("MsGraphApi::search_user_by_upn() error. - {$e->getPrimaryErrorMessage()}");
			return null;
		} catch (\Exception $e) {
			error_log("MsGraphApi::search_user_by_upn() error. - $e");
			return null;
		} catch ( \Throwable $e ) {
			error_log("MsGraphApi::search_user_by_upn() error. - $e");
			return null;
		}
	}

	/**
	 * Search the user by "mail" aka email alias.
	 * @param string $email (ex. "james.pham@vta.org")
	 * @param int $top limit the maximum number of results.
	 * @return mixed|null
	 */
	public function search_user_by_email( string $email, int $top = 1 ): ?User {
		try {
			// remove white spaces
			$email = trim($email);

			// do email search. Must be separate from above
			$queryParams         = UsersRequestBuilderGetRequestConfiguration::createQueryParameters();
			$queryParams->select = $this::SELECT_PARAMS;
			$queryParams->search = "\"mail:$email\"";
			$queryParams->top    = $top; // limit to just 1 search by default

			$requestConfig                  = new UsersRequestBuilderGetRequestConfiguration();
			$requestConfig->queryParameters = $queryParams;
			$requestConfig->headers         = [ 'ConsistencyLevel' => 'eventual' ];

			/** @var UserCollectionResponse $res */
			$res   = $this->_graphClient->users()->get($requestConfig)->wait();
			$value = $res->getValue();

			if ( !$value || count($value) < 1 )
				return null;

			// just return the first user of the array (should be only one).
			return $res->getValue()[0] ?? null;

		} catch ( OdataError $e ) {
			error_log("MsGraphApi::search_user_by_email() error. - {$e->getPrimaryErrorMessage()}");
			return null;
		} catch (\Exception $e) {
			error_log("MsGraphApi::search_user_by_email() error. - $e");
			return null;
		} catch ( \Throwable $e ) {
			error_log("MsGraphApi::search_user_by_email() error. - $e");
			return null;
		}
	}

	/**
	 * Initializes the graph client.
	 * @return GraphServiceClient | null
	 */
	private function _initClient(): ?GraphServiceClient {
		try {
			// Uses https://graph.microsoft.com/.default scopes if none are specified
			$tokenRequestContext = new ClientCredentialContext(
				'',
				'',
				''
			);
			return new GraphServiceClient($tokenRequestContext);

		}  catch ( \Exception $e ) {
			error_log("MsGraphApi::_initClient() error. - $e");
			return null;
		}
	}

}
