<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use Exception;
use \OpenAI;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
        $this->session = \Config\Services::session();
    }

    /**
     * Gọi API đến ChatGPT
     */
    protected function gpt_api_call($question, $context, $tools = null, $tool_choice = 'auto') {
        try {
            $yourApiKey = getenv('OPENAI_API_KEY');
            $client = OpenAI::client($yourApiKey);

            //Tạo stream để nhận token ngay, tránh lỗi timeout
            $stream = $client->chat()->createStreamed([
                'model' => 'gpt-4o-mini', //Tên model sử dung
                'messages' => [
                    ['role' => 'system', 'content' => "You're a helpful assistant, able to process Vietnamese sentences. User's previous messages are: {$context}. Reply in Vietnamese." ],
                    ['role' => 'user', 'content' => $question],
                ],
                'temperature' => 0.1, //Số càng thấp thì câu trả lời của GPT càng đồng nhất, không thay đổi nhiều
                // 'max_tokens' => 400, //Số lượng input, output token tối đa
                'tools' => $tools, //Khai báo các hàm muốn ChatGPT dùng
                'tool_choice' => $tool_choice,
            ]);

            $params = '';
            $text = '';
            $function_list = [];
            foreach ($stream as $chunk) {
                $array = $chunk->choices[0]->toArray();
                $delta = $array['delta'];

                //Khi chạy đến token cuối thì delta sẽ rỗng
                if (empty($delta)) {
                    break;
                }

                if (!empty($delta['tool_calls'])) {
                    $function = $delta['tool_calls'][0]['function'];
                    if (isset($function['name'])) {
                        //Nếu có function khác thì thêm %% để sau có thể tách ra
                        $function_list[] = $function['name'];
                        $params .= "%%";
                    }
                    $params .= $function['arguments'];
                } else {
                    if (!isset($delta['role']) && isset($delta['content'])) {
                        $text .= $delta['content'];
                    }
                }
            }

            //Nếu GPT gọi function
            if ($params != '') {
                //Xử lý các params trước khi return
                $function_args = explode('%%', $params);
                $json_array = [];
                foreach ($function_args as $arg) {
                    if ($arg != '') {
                        $json = json_decode($arg, true);
                        $json_array[] = $json;
                    }
                }
                $param = array_merge(...$json_array);

                return [
                    'result' => 'tool',
                    'param' => $param,
                    'func_list' => $function_list
                ];
            }
            //Nếu GPT gửi câu trả lời bình thường 
            else {
                return [
                    'result' => 'chat',
                    'text' => $text,
                ];
            }

        } catch (Exception $e) {
            log_message('error', "Lỗi: {$e->getMessage()}, line: {$e->getLine()}");
            return [
                'result' => 'fail'
            ];
        }
    }
}
