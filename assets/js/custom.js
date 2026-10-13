function addMotifAutre(mot) {
	if ($(mot).val() == 3) {
		$("#divmotifautre").append(
			'<textarea required name="motif-autre" id="motif-autre" placeholder="Veuillez saisir le motif autre" class="form-control m-0 mt-2"></textarea>'
		);
		$("#motif-autre").focus();
	} else {
		$("#motif-autre").remove();
	}
}

/*
function addMedicament(med) {
	if ($(med).val() != "default") {
		$("#medoc-autre").remove();
		$("#premMedoc").after(
			$('<div class="d-flex flex-column" id="medoc-autre">').append(
				$('<label for="deuxMedoc">2eme medicament présenté: </label>')
			)
		);
		$("#labelMedoc").after(
			$('<select required name="deuxMedoc" class="form-select mt-3">').append(
				'<option value class="text-center"></option>'
			)
		);
		$(".listemedoc").clone().appendTo("#deuxMedoc");
		var val = $(med).val();
		$("#deuxMedoc>.listemedoc[value='" + val + "']").remove();
	} else {
		$("#medoc-autre").remove();
	}
}
*/
function checkDateSaisieRapport() {
	$("#errorDate").remove();
	if ($("#datevisite").val() != "" && $("#datesaisit").val() < $("#datevisite").val()) {
		$("#datevisite")
			.parent()
			.after(
				'<div class="text-danger small" id="errorDate">La date de visite doit être inférieure à la date de saisie</div>'
			);
		$("#datevisite").focus();
		return false;
	} else {
		return true;
	}
}

function checkMedicament() {
	$("#errorDate").remove();
	if ($("#premMedoc").val() == "default") {
		$("#premMedoc")
			.parent()
			.after(
				'<div class="text-danger small" id="errorDate">Attention, vous n\'avez pas saisi de médicaments</div>'
			);
		$("#premMedoc").focus();
		return false;
	} else {
		return true;
	}
}
/*
// FONCTION D'AJOUT ECHANTILLON
function addEchantillon(ech) {
 	if (ech.checked) {
 		var i = 1;
 		$("#redigerEtEchantillon").after(
 			'<div class="col-10 d-flex flex-column justify-content-center align-items-center mt-3 mb-5 mx-auto" id="addechantillon"><div id="Echantillon' +
 				i +
 				'" class=" mb-1 d-flex flex-row"><input value="1" class="form-control me-1 rounded w-25 text-center" name="nbEchantillon' + i + '" id="nbEchantillon' +
 				i +
 				'" type="number"><button type="button" id="button" value="' +
 				i +
 				'" onclick="addOtherEchantillon();" class="btn btn-outline-secondary"><i class="bi bi-plus-lg"></i></button></div></div>'
 		);
		$("#Echantillon" + i + "").prepend(
 			$(
 				'<select name="echantillonadd' + i + '" id="echantillonadd' + i + '" class="form-select m-0 me-1" required>'
 			).append('<option value="">- Choisissez un échantillon -</option>')
 		);
 		$(".listemedoc")
 			.clone()
 			.appendTo("#echantillonadd" + i + "");
 		$("#echantillonadd" + i + "").focus();
 	} else {
 		if (confirm("Voulez-vous vraiment décocher Échantillon ?")) {
 			$("#addechantillon").remove();
 		} else {
 			$(ech).prop("checked", true);
 		}
 	}
 }

 function addEchantillon2(offre) {
	offre.forEach(element => {
		let med = element['MED_DEPOTLEGAL'];
		let qte = element['OFF_QTE'];
	
		
 		var i = 1;
 		$("#redigerEtEchantillon").after(
 			'<div class="col-10 d-flex flex-column justify-content-center align-items-center mt-3 mb-5 mx-auto" id="addechantillon"><div id="Echantillon' +
 				i +
 				'" class=" mb-1 d-flex flex-row"><input required min="1" value="' + qte + '" class="form-control me-1 rounded w-25 text-center" name="nbEchantillon' + i + '" id="nbEchantillon' +
 				i +
 				'" type="number"><button type="button" id="button" value="' +
 				i +
 				'" onclick="addOtherEchantillon();" class="btn btn-outline-secondary"><i class="bi bi-plus-lg"></i></button></div></div>'
 		);
		$("#Echantillon" + i + "").prepend(
 			$(
 				'<select name="echantillonadd' + i + '" id="echantillonadd' + i + '" class="form-select m-0 me-1" required>'
 			).append('<option value="' + med + '">- Choisissez un échantillon -</option>')
 		);
 		$(".listemedoc")
 			.clone()
 			.appendTo("#echantillonadd" + i + "");
 		$("#echantillonadd" + i + "").focus();
		});
 	/*} else {
 		if (confirm("Voulez-vous vraiment décocher Échantillon ?")) {
 			$("#addechantillon").remove();
 		} else {
 			$(ech).prop("checked", true);
 		}
 	}
 }
*/
/*
 function ajoutEchantillon(med, qte) {
ech = []
		addEchantillon(ech);
	};
*/

 function addOtherEchantillon() {
 	if (parseInt($("#button").val()) < 9) {
 		var i = parseInt($("#button").val()) + 1;
 		$("#button").remove();
 		$("#buttonMinus").remove();
 		$("#addechantillon").append(
 			'<div id="Echantillon' +
 				i +
 				'" class=" mb-1 d-flex flex-row"><input value="1" class="form-control me-1 rounded w-25 text-center" name="nbEchantillon' + i + '" id="nbEchantillon' +
 				i +
				'" type="number"><button type="button" id="button" value="' +
 				i +
 				'" onclick="addOtherEchantillon();" class="btn btn-outline-secondary me-1"><i class="bi bi-plus-lg"></i></button><button type="button" id="buttonMinus" value="' +
 				i +
 				'" onclick="minusEchantillon(this);" class="btn btn-outline-secondary"><i class="bi bi-dash-lg"></i></button></div></div>'
 		);
 		$("#Echantillon" + i + "").prepend(
 			$(
 				'<select name="echantillonadd' + i + '" id="echantillonadd' + i + '" class="form-select m-0 me-1" required>'
 			).append('<option value="">- Choisissez un échantillon -</option>')
 		);
		$(".listemedoc")
 			.clone()
 			.appendTo("#echantillonadd" + i + "");
 		$("#echantillonadd" + i + "").focus();
 	} else {
 		var i = parseInt($("#button").val()) + 1;
 		$("#button").remove();
 		$("#buttonMinus").remove();
 		$("#addechantillon").append(
 			'<div id="Echantillon' +
 				i +
 				'" class=" mb-1 d-flex flex-row"><input value="1" class="form-control me-1 rounded w-25 text-center" name="nbEchantillon' + i + '" id="nbEchantillon' +
 				i +
 				'" type="number"><button type="button" id="buttonMinus" value="' +
 				i +
 				'" onclick="minusEchantillon(this);" class="btn btn-outline-secondary"><i class="bi bi-dash-lg"></i></button></div></div>'
 		);
 		$("#Echantillon" + i + "").prepend(
 			$(
 				'<select name="echantillonadd' + i + '" id="echantillonadd' + i + '" class="form-select m-0 me-1" required>'
 			).append('<option value="">- Choisissez un échantillon -</option>')
 		);
 		$(".listemedoc")
 			.clone()
 			.appendTo("#echantillonadd" + i + "");
 		$("#echantillonadd" + i + "").focus();
 	}
 }

 function minusEchantillon(min) {
 	var i = parseInt($("#buttonMinus").val()) - 1;
 	$(min).parent().remove();
 	if (i > 1) {
 		$("#Echantillon" + i + "").append(
			'<button type="button" id="button" value="' +
 				i +
 				'" onclick="addOtherEchantillon();" class="btn btn-outline-secondary me-1"><i class="bi bi-plus-lg"></i></button><button type="button" id="buttonMinus" value="' +
 				i +
 				'" onclick="minusEchantillon(this);" class="btn btn-outline-secondary"><i class="bi bi-dash-lg"></i></button>'
 		);
 	} else {
 		$("#Echantillon" + i + "").append(
 			'<button type="button" id="button" value="' +
 				i +
 				'" onclick="addOtherEchantillon();" class="btn btn-outline-secondary me-1"><i class="bi bi-plus-lg"></i></button>'
 		);
 	}
 }

