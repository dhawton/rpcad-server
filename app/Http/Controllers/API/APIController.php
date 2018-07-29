<?php
namespace App\Http\Controllers\API;

use \App\Http\Controllers\Controller as BaseController;

/**
 * Class ApiController
 *
 * @package App\Http\Controllers\API
 *
 * @SWG\Swagger(
 *     basePath="/",
 *     host="api.cad.devel",
 *     schemes={"https"},
 *     @SWG\Info(
 *         version="3.0",
 *         title="CAD Server Web API",
 *         description="",
 *         @SWG\Contact(name="Daniel Hawton", url="https://www.vatusa.net"),
 *     ),
 *     @SWG\Tag(name="auth",description="Authentication and deauthentication methods"),
 *     @SWG\Tag(name="servers",description="Handle FiveM Server listings"),
 *     @SWG\Tag(name="user",description="User account management actions"),
 * )
 */
class APIController extends BaseController {
    //
}
/**
 * @SWG\SecurityScheme(
 *   securityDefinition="Session Cookie",
 *   type="apiKey",
 *   in="header",
 *   name="cad_session"
 * )
 */
/**
 *
 * @SWG\Definition(
 *     definition="error",
 *     type="object",
 *     @SWG\Property(
 *         property="status",
 *         type="string",
 *         example="error",
 *     ),
 *     @SWG\Property(
 *         property="message",
 *         type="string",
 *         example="not_logged_in",
 *     ),
 *     @SWG\Property(
 *         property="exception",
 *         type="string"
 *     ),
 * ),
 * @SWG\Definition(
 *     definition="OK",
 *     type="object",
 *     @SWG\Property(
 *         property="status",
 *         type="string",
 *         example="OK",
 *     ),
 * )
 */
