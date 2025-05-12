@extends('layouts.app')

@section('title', 'Pagamento PIX')

@section('conteudo')
<div class="teste my-5 d-none">
    <div class="card col-md-8 col-lg-6 col-sm-12 mx-auto shadow-lg">
        <div class="card-header text-center bg-success text-white">
            <h3><b>Pagamento via PIX</b></h3>
        </div>

        <div class="card-body bg-cinza2">
            <div class="mb-4 text-center">
                <h5>Reserva #{{ $reserva->idReserva }}</h5>
                <p>Sala: <strong>{{ $reserva->sala->nomeSala }}</strong></p>
                <p>Valor: <strong>R$ {{ number_format($reserva->valor, 2, ',', '.') }}</strong></p>
                <p>Vencimento: <strong>{{ \Carbon\Carbon::parse($qrCodeData['expiration_date'])->format('d/m/Y H:i') }}</strong></p>
            </div>
            
            <div class="text-center mb-4">
                <h5>Escaneie o QR Code abaixo</h5>
                <img src="{{ $qrCodeData['qr_code'] . urlencode($qrCodeData['qr_code_text']) }}" 
                     alt="QR Code para PIX" 
                     class="img-fluid mx-auto d-block mb-3" 
                     style="max-width: 250px;">
                
                <div class="alert alert-info">
                    <p class="mb-1"><strong>Ou copie o código PIX abaixo:</strong></p>
                    <div class="input-group mb-2">
                        <input type="text" id="pixCode" class="form-control" value="{{ $qrCodeData['qr_code_text'] }}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copyPixCode()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <small>Cole este código na opção PIX Copia e Cola do seu aplicativo bancário</small>
                </div>
            </div>
            
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> Após realizar o pagamento, o sistema atualizará seu status automaticamente em até 15 minutos. Você também pode consultar o status da sua reserva a qualquer momento.
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                <a href="{{ route('reserva.consulta') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> Consultar Reserva
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyPixCode() {
        const pixCodeElement = document.getElementById('pixCode');
        pixCodeElement.select();
        document.execCommand('copy');
        
        // Mostrar feedback de cópia
        const button = pixCodeElement.nextElementSibling;
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.teste.mt-5');
        
        cards.forEach(card => {
            card.classList.add('d-none');
        });

        function animateCards() {
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.remove('d-none');
                    card.classList.add('initial-animation');
                    card.offsetHeight;

                    setTimeout(() => {
                        card.classList.add('show-card');

                        setTimeout(() => {
                            card.classList.remove('initial-animation');
                            card.classList.remove('show-card');
                        }, 700);
                    }, 10);
                }, 150 * index);
            });
        }

        setTimeout(animateCards, 200);
    });
</script>
@endsection