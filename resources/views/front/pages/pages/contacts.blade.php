@extends('front.app')

@section('content')

    <div class="contacts">
        <div class="about-us">
            <div class="container">
                <h1>Block Shape</h1>
                <p>The project Block Shape is the first app built on blockchain that combines Fitness, Yoga, healthy
                    nutrition, payments in crypto, and NFT in one place. You can do it from home - get the body of Your
                    dreams and receive NFT for that. Own the digital proof of Your real-life body transformation.</p>
            </div>
        </div>
        <div class="contact-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2>Contacts</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="https://discord.com/invite/6cMryUfBZG" class="email">
                                    https://discord.com/invite/6cMryUfBZG
                                </a>
                            </div>
                            <div class="col-md-12">
                                <a href="#" class="email">blockshapetop@gmail.com</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h2>Get in touch</h2>
                        <div class="contact-form">
                            <form>
                                <input type="text" name="name" placeholder="First Name">
                                <input type="text" name="secondName" placeholder="Last Name">
                                <input type="email" name="email" placeholder="E-mail">
                                <textarea placeholder="Message"></textarea>
                                <input type="submit" value="Submit"> <br><br>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('front.partials.footer-line')

@stop

