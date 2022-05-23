<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UsersRepository;
use App\Traits\Auth\Tokenizer;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\EmailConfirmation;
use App\MRation;
use App\UserHistory;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Restable;
use Validator;
use Input;
use Session;
use View;
use App\Services\Contracts\HtmlBuilder;


class AuthController extends Controller
{
    use Tokenizer;

    protected $redirectPath = '/home';

    protected $loginPath = '/';
    /**
     * @var UsersRepository
     */
    private $usersRepository;

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     * @param UsersRepository $usersRepository
     */
    public function __construct(UsersRepository $usersRepository)
    {
        // $this->middleware('guest', ['except' => 'getLogout']);
        $this->usersRepository = $usersRepository;

        View::share('termFrom', date('Y-m-d'));
        View::share('termTo', date('Y-m-d'));
    }

    // User login
    public function login()
    {
        return view('front.pages.login');
    }

    // User post login(handle proccess)
    public function loginPost(Request $request,HtmlBuilder $HtmlBuilder)
    {
        $item = Validator::make(Input::all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($item->fails()){
            return redirect()->back()->with('errors', $item->messages())->withInput();
        }

        if (!Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')))){
            return redirect()->back()->with('bad', 'email or password is inconcrect!');
        }

//        $checkEmail = EmailConfirmation::whereEmail(Input::get('email'))->first();

//        if (!is_null($checkEmail)) {
//            EmailConfirmation::whereEmail(Input::get('email'))->update([
//                'token' => $token = str_random(5),
//                'confirmed_at' => null
//            ]);
//        }else{
//            EmailConfirmation::create([
//                'email' => Input::get('email'),
//                'token' => $token = str_random(5)
//            ]);
//        }

        $this->checkRation($HtmlBuilder);

        return redirect()->route('user.redirect');
    }

    public function checkRation($HtmlBuilder)
    {
        $userHistory = UserHistory::where('user_id', Auth::user()->id)->first();
        if (is_null($userHistory)) {
            return false;
        }

        $ration = MRation::where('user_id', Auth::user()->id)->get();
        if (empty($ration)) {
            return false;
        }

        $rationNow = MRation::where('user_id', Auth::user()->id)->where('date', date('Y-m-d'))->first();

        if (is_null($rationNow)) {
            dd('vfd');
            $HtmlBuilder->build($user, $userHistory, null, date('Y-m-d'));
        }
    }

    // User register
    public function register()
    {
        return view('front.pages.register');
    }

    // User post register (handle process)
    public function registerPost()
    {
        $item = Validator::make(Input::all(), [
            'name' => 'required',
            'sname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'passwordAgain' => 'required|same:password',
        ]);

        if ($item->fails()){
            return redirect()->back()->with('errors', $item->messages())->withInput();
        }

        User::create([
            'name' => Input::get('name').' '.Input::get('sname'),
            'first_name' => Input::get('name'),
            'second_name' => Input::get('sname'),
            'email' => Input::get('email'),
            'password' => bcrypt(Input::get('password')),
            'remember_token' => Input::get('_token'),
            'role' => 'member',
        ]);

        if (!Auth::attempt(array('email' => Input::get('email'), 'password' => Input::get('password')))){
            return redirect()->back()->with('bad', 'email or password is inconcrect!');
        }

//        $checkEmail = EmailConfirmation::whereEmail(Input::get('email'))->first();
//
//        if (!is_null($checkEmail)) {
//            EmailConfirmation::whereEmail(Input::get('email'))->update([
//                'token' => $token = str_random(5),
//                'confirmed_at' => null
//            ]);
//        }else{
//            EmailConfirmation::create([
//                'email' => Input::get('email'),
//                'token' => $token = str_random(5)
//            ]);
//        }

        return redirect()->route('user.redirect');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRegister()
    {
        Session::set('offer_id', 5);
        return view('auth.register', []);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function postRegister(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }
        $token = null;
        if ($user = $this->create($request->all())) {
            $token = $this->createToken($user, 3600 * 12);
            $user = (new UserTransformer)->transform($user);
        }
        return Restable::success([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'birth_date' => 'required|array'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return $this->usersRepository->createOrUpdateCustomer(
            array_merge($data, ['online' => 1])
        );
    }

    protected function getCredentials(Request $request)
    {
        return $request->only($this->loginUsername(), 'password') + ['role' => 'instructor', 'active' => 1];
    }
}

