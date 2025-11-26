@extends('layouts.main')

@section('title', 'Adicionar Fundos')

@section('content')

<div class="funds-page-container">
    <div class="funds-wrapper">
        <div class="wallet-header">
            <h1>Carteira Digital</h1>
            <p class="text-muted">Adicione saldo para comprar jogos na loja.</p>
        </div>

        <div class="current-balance-card">
            <span class="balance-label">Seu Saldo Atual</span>
            <div class="balance-value">
                R$ {{$user->cash}}
            </div>
        </div>

        <div class="add-funds-form-box">
            <h3>Quanto você quer adicionar?</h3>

            <div class="amount-presets">
                <button type="button" class="preset-btn" onclick="setAmount(20)">R$ 20,00</button>
                <button type="button" class="preset-btn" onclick="setAmount(50)">R$ 50,00</button>
                <button type="button" class="preset-btn" onclick="setAmount(100)">R$ 100,00</button>
                <button type="button" class="preset-btn" onclick="setAmount(200)">R$ 200,00</button>
            </div>

            <hr class="divider">

            <form action="/updatefunds" method="POST"> 
                @csrf
                
                <div class="custom-amount-group">
                    <label for="amount">Valor Personalizado (R$)</label>
                    <div class="input-wrapper">
                        <span class="currency-symbol">R$</span>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               class="amount-input" 
                               placeholder="0,00" 
                               step="0.01" 
                               min="1" 
                               required>
                    </div>
                </div>

                <div class="payment-methods">
                    <label>Forma de Pagamento</label>
                    <div class="methods-grid">
                        <label class="method-card selected">
                            <input type="radio" name="payment_method" value="pix" checked>
                            <span class="method-icon">💠</span>
                            <span>PIX (Instantâneo)</span>
                        </label>
                        <label class="method-card">
                            <input type="radio" name="payment_method" value="credit_card">
                            <span class="method-icon">💳</span>
                            <span>Cartão de Crédito</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-confirm-funds">
                    Confirmar e Pagar
                </button>
            </form>
        </div>

        <p class="security-note">🔒 Pagamento seguro e processado instantaneamente.</p>
    </div>
</div>

@endsection