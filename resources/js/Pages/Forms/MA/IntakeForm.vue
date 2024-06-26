<template>
  <div class="flex flex-col">
    <div class="container mx-auto">
      <div class="w-full">
        <form
          @submit.prevent="submit"
          class="bg-white shadow-md rounded px-12 pt-12 pb-8 mb-4 mt-20"
        >
          <label class="block mt-4">
            <span class="text-gray-700 mt-2">
              I acknowledge that Modern Acupuncture seeks to provide the highest
              level of acupuncture care by only employing acupuncturists who are
              licensed and have graduated from accredited Master’s level
              programs consisting of at least 2,000 clock hours of combined
              instruction and clinical practice. Additionally they are required
              to pass rigorous board exams and meet state specific licensing
              requirements.
            </span>
          </label>
          <div class="mt-4">
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  class="form-radio h-6 w-6"
                  name="acknowledgement"
                  v-model="$v.form.acknowledgement.$model"
                  value="yes"
                />
                <span class="ml-2 font-bold"
                  >YES I ACKNOWLEDGE
                  <span class="text-red-500 text-bold text-xl">*</span></span
                >
              </label>
              <div
                class="error"
                v-if="
                  $v.form.acknowledgement.$anyError &&
                  !$v.form.acknowledgement.required
                "
              >
                Acknowledgment is required
              </div>
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              PATIENT INFORMATION
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Name
                <span class="text-red-500 text-bold text-xl">*</span></span
              >

              <input
                type="text"
                class="form-input mt-1 block w-full mb-2"
                v-model.trim="$v.form.first_name.$model"
                placeholder="First Name"
              />
              <div
                class="error"
                v-if="
                  $v.form.first_name.$anyError && !$v.form.first_name.required
                "
              >
                First Name is required
              </div>

              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model.trim="$v.form.last_name.$model"
                placeholder="Last Name"
              />
              <div
                class="error"
                v-if="
                  $v.form.last_name.$anyError && !$v.form.last_name.required
                "
              >
                Last Name is required
              </div>
            </div>
            <div class="w-full md:w-1/2 mb-2 px-3 md:mb-0">
              <label class="block">
                <span class="text-gray-700 font-bold"
                  >Gender
                  <span class="text-red-500 text-bold text-xl">*</span></span
                >
                <select
                  class="form-select mt-1 block w-full"
                  v-model="$v.form.gender.$model"
                >
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="unknown">Unknown</option>
                </select>
                <div
                  class="error"
                  v-if="$v.form.gender.$anyError && !$v.form.gender.required"
                >
                  Gender is required
                </div>
              </label>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Date Of Birth
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="date"
                class="form-input mt-1 block w-full"
                v-model="$v.form.dob.$model"
                placeholder="Date Of Birth"
              />
              <div
                class="error"
                v-if="$v.form.dob.$anyError && !$v.form.dob.required"
              >
                Date of birth is required
              </div>
            </div>

            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Age <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="number"
                class="form-input mt-1 block w-full"
                v-model="$v.form.age.$model"
                placeholder="Client Age"
              />
              <div
                class="error"
                v-if="$v.form.age.$anyError && !$v.form.age.required"
              >
                Age is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Email Address
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="email"
                class="form-input mt-1 block w-full"
                v-model="$v.form.email.$model"
                placeholder="Client Email"
              />
              <div
                class="error"
                v-if="$v.form.email.$anyError && !$v.form.email.required"
              >
                Email is required
              </div>
            </div>

            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Mobile
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.mobile.$model"
                placeholder="Mobile"
              />
              <div
                class="error"
                v-if="$v.form.mobile.$anyError && !$v.form.mobile.required"
              >
                Mobile is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Address
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.address.$model"
                placeholder="Address"
              />
              <div
                class="error"
                v-if="$v.form.address.$anyError && !$v.form.address.required"
              >
                Address is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/3 mb-2 md:mb-0">
              <input
                type="text"
                class="form-input block w-full"
                v-model="$v.form.city.$model"
                placeholder="City"
              />
              <div
                class="error"
                v-if="$v.form.city.$anyError && !$v.form.city.required"
              >
                City is required
              </div>
            </div>
            <div class="w-full md:w-1/3 px-3 mb-2 md:mb-0">
              <input
                type="text"
                class="form-input block w-full"
                v-model="$v.form.state.$model"
                placeholder="State"
              />
              <div
                class="error"
                v-if="$v.form.state.$anyError && !$v.form.state.required"
              >
                State is required
              </div>
            </div>
            <div class="w-full md:w-1/3 px-3 mb-2 md:mb-0">
              <input
                type="text"
                class="form-input block w-full"
                v-model="$v.form.postal_code.$model"
                placeholder="Post Code"
              />
              <div
                class="error"
                v-if="
                  $v.form.postal_code.$anyError && !$v.form.postal_code.required
                "
              >
                Post Code is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Patient Occupation
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.patient_occupation.$model"
                placeholder="Patient Occupation"
              />
              <div
                class="error"
                v-if="
                  $v.form.patient_occupation.$anyError &&
                  !$v.form.patient_occupation.required
                "
              >
                Patient Occupation is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Emergency Contact Name
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.emergency_contact_name.$model"
                placeholder="Emergency Contact Name"
              />
              <div
                class="error"
                v-if="
                  $v.form.emergency_contact_name.$anyError &&
                  !$v.form.emergency_contact_name.required
                "
              >
                Emergency Contact Name is required
              </div>
            </div>
            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Emergency Contact Phone
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.emergency_contact_phone.$model"
                placeholder="Emergency Contact Phone"
              />
              <div
                class="error"
                v-if="
                  $v.form.emergency_contact_phone.$anyError &&
                  !$v.form.emergency_contact_phone.required
                "
              >
                Emergency Contact Phone is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you had acupuncture treatments before?
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="acupuncture_before"
                    value="yes"
                    v-model="$v.form.acupuncture_before.$model"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="acupuncture_before"
                    value="no"
                    v-model="$v.form.acupuncture_before.$model"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
              <div
                class="error"
                v-if="
                  $v.form.acupuncture_before.$anyError &&
                  !$v.form.acupuncture_before.required
                "
              >
                This Field is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/5 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Initials
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.initial_patient_information.$model"
              />
              <div
                class="error"
                v-if="
                  $v.form.initial_patient_information.$anyError &&
                  !$v.form.initial_patient_information.required
                "
              >
                Initials is required
              </div>
            </div>

            <div class="w-full md:w-4/5 px-3 mt-5 mb-2 md:mb-0">
              <span class="text-gray-700"
                >I understand that occasionally, there could be minor bruising
                where the needles are inserted. To keep this to a minimum,
                Modern Acupuncture uses the highest quality needles available,
                which also provide the most comfortable treatment with virtually
                no pain.</span
              >
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              MEDICAL HISTORY
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Height: (format example: 5'10'')
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.height.$model"
              />
              <div
                class="error"
                v-if="$v.form.height.$anyError && !$v.form.height.required"
              >
                Height is required
              </div>
            </div>

            <div class="w-full md:w-1/2 px-3 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Weight: (LBS.)
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.weight.$model"
              />
              <div
                class="error"
                v-if="$v.form.weight.$anyError && !$v.form.weight.required"
              >
                Weight is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Are you currently taking blood thinners or on an aspirin
                regimen?
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.blood_thinner_medicine.$model"
                    name="blood_thinner_medicine"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.blood_thinner_medicine.$model"
                    name="blood_thinner_medicine"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
              <div
                class="error"
                v-if="
                  $v.form.blood_thinner_medicine.$anyError &&
                  !$v.form.blood_thinner_medicine.required
                "
              >
                This Field is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Are you currently pregnant or trying to conceive?
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.pregnancy.$model"
                    name="pregnancy"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.pregnancy.$model"
                    name="pregnancy"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
              <div
                class="error"
                v-if="
                  $v.form.pregnancy.$anyError && !$v.form.pregnancy.required
                "
              >
                This Field is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have a seizure disorder?
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.seizure_disorder.$model"
                    name="seizure_disorder"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.seizure_disorder.$model"
                    name="seizure_disorder"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
              <div
                class="error"
                v-if="
                  $v.form.seizure_disorder.$anyError &&
                  !$v.form.seizure_disorder.required
                "
              >
                This Field is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold">Do you have HIV/AIDs?</span>
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="form.hiv"
                    name="hiv"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="form.hiv"
                    name="hiv"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have Hepatitis B?
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.hepatitis_b.$model"
                    name="hepatitis_b"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.hepatitis_b.$model"
                    name="hepatitis_b"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
              <div
                class="error"
                v-if="
                  $v.form.hepatitis_b.$anyError && !$v.form.hepatitis_b.required
                "
              >
                This Field is required
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have a known nickel allergy?
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.nickel_allergy.$model"
                    name="nickel_allergy"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    v-model="$v.form.nickel_allergy.$model"
                    name="nickel_allergy"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
              <div
                class="error"
                v-if="
                  $v.form.nickel_allergy.$anyError &&
                  !$v.form.nickel_allergy.required
                "
              >
                This Field is required
              </div>
            </div>
          </div>

          <div class="block">
            <span class="text-gray-700 font-bold"
              >What are your concerns and reasons for visiting today? (select
              all that apply)</span
            >
            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        name="today_digestive_issue"
                        v-model="form.today_digestive_issue"
                      />
                      <span class="ml-2">Digestive issues</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_stress"
                        name="today_stress"
                      />
                      <span class="ml-2">Stress</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_insomnia"
                        name="today_insomnia"
                      />
                      <span class="ml-2">Insomnia</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_anxiety"
                        name="today_anxiety"
                      />
                      <span class="ml-2">Anxiety</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_high_blood_pressure"
                        name="today_high_blood_pressure"
                      />
                      <span class="ml-2">High Blood Pressure</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_depression"
                        name="today_depression"
                      />
                      <span class="ml-2">Depression</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_allergies"
                        name="today_allergies"
                      />
                      <span class="ml-2">Allergies</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_back_pain"
                        name="today_back_pain"
                      />
                      <span class="ml-2">Back Pain</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_muscle_sprain"
                        name="today_muscle_sprain"
                      />
                      <span class="ml-2">Muscle Sprain or Strain</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_headaches"
                        name="today_headaches"
                      />
                      <span class="ml-2">Headaches</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_fatigue"
                        name="today_fatigue"
                      />
                      <span class="ml-2">Fatigue</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_joint_pain"
                        name="today_joint_pain"
                      />
                      <span class="ml-2">Joint pain</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_menstrual_cramps"
                        name="today_menstrual_cramps"
                      />
                      <span class="ml-2">Menstrual Cramps/PMS</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-2/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.today_muscle_aches"
                        name="today_muscle_aches"
                      />
                      <span class="ml-2">Muscle Aches/PMS</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Of the conditions you selected, which is the most
                prominent?</span
              >
              <input
                type="text"
                name="most_prominent_reason"
                class="form-input mt-1 block w-full"
                v-model="form.most_prominent_reason"
                placeholder=""
              />
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >How long have you been experiencing symptoms?</span
              >
              <input
                type="text"
                name="current_symptoms_duration"
                class="form-input mt-1 block w-full"
                v-model="form.current_symptoms_duration"
                placeholder=""
              />
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Are you currently under a physicians care for this
                condition?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="under_physician_care"
                    v-model="form.under_physician_care"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="under_physician_care"
                    v-model="form.under_physician_care"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >If yes, what is your diagnosis?</span
              >
              <input
                type="text"
                name="under_physician_care_diagnosis"
                class="form-input mt-1 block w-full"
                v-model="form.under_physician_care_diagnosis"
                placeholder=""
              />
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              COSMETIC ACUPUNCTURE
            </div>
          </div>

          <label class="block mt-4 mb-2">
            <span class="text-gray-700 mt-2"
              >If you are here for Cosmetic Acupuncture, please complete the
              following questions:</span
            >
          </label>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you received Botox or dermal filler injections in the last
                2 weeks?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="botox_dermal_filler"
                    v-model="form.botox_dermal_filler"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="botox_dermal_filler"
                    v-model="form.botox_dermal_filler"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you had any cosmetic surgical procedures within the last 6
                weeks?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="cosmetic_surgical_procedures"
                    v-model="form.cosmetic_surgical_procedures"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="cosmetic_surgical_procedures"
                    v-model="form.cosmetic_surgical_procedures"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you have a tendency to bruise easily?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="tendency_to_bruise"
                    v-model="form.tendency_to_bruise"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="tendency_to_bruise"
                    v-model="form.tendency_to_bruise"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Do you get migraine headaches once per month or more?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="migraine"
                    v-model="form.migraine"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="migraine"
                    v-model="form.migraine"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap mb-2">
            <div class="w-full md:w-1/1 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Have you had a recent head injury or concussion?</span
              >
              <div class="mt-2">
                <label class="inline-flex items-center">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="head_injury"
                    v-model="form.head_injury"
                    value="yes"
                  />
                  <span class="ml-2 uppercase">Yes</span>
                </label>
                <label class="inline-flex items-center ml-6">
                  <input
                    type="radio"
                    class="form-radio h-6 w-6"
                    name="head_injury"
                    v-model="form.head_injury"
                    value="no"
                  />
                  <span class="ml-2 uppercase">No</span>
                </label>
              </div>
            </div>
          </div>

          <div class="block">
            <span class="text-gray-700"
              >Cosmetic visits include targeted areas of treatment on the face.
              Please select up to two areas you would like to focus on:</span
            >
            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.cosmetic_smile_lines"
                        name="cosmetic_smile_lines"
                      />
                      <span class="ml-2">Smile lines</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.cosmetic_crows_feet"
                        name="cosmetic_crows_feet"
                      />
                      <span class="ml-2">Crows Feet</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.cosmetic_forehead_lines"
                        name="cosmetic_forehead_lines"
                      />
                      <span class="ml-2">Forehead lines</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.cosmetic_surrounding_lips"
                        name="cosmetic_surrounding_lips"
                      />
                      <span class="ml-2">Lines surrounding lips</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <div class="flex flex-wrap">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.cosmetic_lines_between_eyebrows"
                        name="cosmetic_lines_between_eyebrows"
                      />
                      <span class="ml-2">Lines between eyebrows</span>
                    </label>
                  </div>
                </div>
                <div class="w-full md:w-2/4 mb-2 md:mb-0">
                  <div class="mb-2">
                    <label class="inline-flex items-center">
                      <input
                        type="checkbox"
                        class="form-checkbox h-6 w-6"
                        v-model="form.cosmetic_marionette_lines"
                        name="cosmetic_marionette_lines"
                      />
                      <span class="ml-2"
                        >Marionette Lines (Sides of chin to edges of
                        mouth)</span
                      >
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              NOTICE OF PRIVACY PRACTICES
            </div>
          </div>
          <div class="block mt-4 mb-2">
            <div class="text-gray-600 mt-2">
              <p class="font-bold mb-2">
                Click below to read this office’s Notice of Privacy Practices.
              </p>
              <p class="mb-2">
                <a
                  href="https://www.modernacupuncture.com/privacy"
                  target="_blank"
                  class="text-blue-600"
                  >HIPAA ACKNOWLEDGMENT
                </a>
                - (Link will take you to a new browser window. Close or return
                to this page when done reviewing.)
              </p>
              <p class="mb-2">
                By signing this I am acknowledging that I have had full
                opportunity to read and consider the contents of this office’s
                Privacy Practices, which can be found at modacu.com/privacy. I
                further acknowledge and understand that, by signing this Patient
                Intake Form that I am giving my consent for this office’s use
                and disclosure of my protected health information to carry out
                treatment, payment activities and health care options, as
                outlined in the Notice of Privacy Practices disclosure in the
                link above.
              </p>
            </div>
          </div>
          <div class="mt-4 mb-2">
            <div class="mt-2">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  class="form-radio h-6 w-6"
                  name="privacy_acknowledge"
                  v-model="$v.form.privacy_acknowledge.$model"
                  value="yes"
                />
                <span class="ml-2 font-bold"
                  >YES I ACKNOWLEDGE
                  <span class="text-red-500 text-bold text-xl">*</span></span
                >
                <div
                  class="error"
                  v-if="
                    $v.form.privacy_acknowledge.$anyError &&
                    !$v.form.privacy_acknowledge.required
                  "
                >
                  Acknowledgment is required
                </div>
              </label>
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              INFORMED CONSENT FOR ACUPUNCTURE
            </div>
          </div>
          <div class="block mt-4 mb-2">
            <div class="text-gray-600 mt-2">
              <p class="mb-2">
                This Informed Consent for Acupuncture applies to the essential
                and cosmetic acupuncture. This consent will allow us to provide
                acupuncture treatments to you (or your dependents) now and in
                the future.
              </p>
              <p class="mb-2">
                <span class="font-bold">INTRODUCTION - </span>An acupuncture
                treatment involves the insertion of acupuncture needles at
                specific points on the body to boost the body’s natural
                painkillers, increase blood flow and trigger a healing response.
              </p>
              <p class="mb-2">
                An acupuncture facial treatment involves the insertion of
                acupuncture needles into fine lines and wrinkles on the face and
                neck in order to reduce the visible signs of aging. A facial
                acupuncture treatment addresses the entire body
                constitutionally, and is not merely “cosmetic.” An acupuncture
                facial involves the patient in an organic, gradual process, that
                is customized for each individual. It is no way analogous to, or
                a substitute for, a surgical “face lift”.
              </p>
              <p class="mb-2">
                <span class="font-bold">BENEFITS - </span>Facial acupuncture can
                increase facial tone, decrease puffiness around the eyes, as
                well as bring more firmness to sagging skin, enhance the
                radiance of the complexion, and flesh out sunken areas.
                Customarily, fine wrinkles will disappear, and deeper ones will
                be reduced. As this treatment is not merely confined to the
                face, but incorporates the entire body and constitutional issues
                of health.
              </p>
              <p class="mb-2">
                <span class="mb-2">ADDITIONAL CARE NECESSARY - </span
                >Acupuncture should be relaxing and patients will often feel
                very calm after a treatment. He/she may feel a little tired or
                sleepy and one should take this into account if he/she is
                planning to drive or use heavy machinery straight after your
                treatment. Patients should refrain from vigorous exercise after
                treatment and, ideally, give themselves a little time to rest.
                It is also advisable not to drink alcohol for several hours
                after treatment.
              </p>
              <p class="mb-2">
                <span class="font-bold">RISKS OF ACUPUNCTURE - </span>Methods of
                treatment within the Modern Acupuncture clinic are limited to
                acupuncture. Acupuncture is a generally safe method of
                treatment, but that it may have some side effects, including
                bruising, numbness or tingling near the needling sites that may
                last a few days, and dizziness or fainting. Unusual risks of
                acupuncture include spontaneous miscarriage, nerve damage and
                organ puncture, including lung puncture (pneumothorax).
                Infection is another possible risk, although the clinic uses
                sterile disposable needles and maintains a clean and safe
                environment. While this document describes the major risks of
                treatment, other side effects and risks may occur.
              </p>
              <p class="mb-2">
                Acupuncture, generally, has very few side effects and any that
                do occur are usually mild and self-correcting. Possible side
                effects and complications may include the following: SORENESS:
                After acupuncture, one might have soreness, minor bleeding or
                bruising at the needle sites. BLEEDING: It is possible, though
                very unusual, that you may have problems with bleeding during an
                acupuncture treatment. Should post-acupuncture bleeding occur,
                it will usually only consist of a few drops. Accumulations of
                blood under the skin may cause a bruise, or hematoma, which will
                resolve itself. INFECTION: Infection is very unusual after an
                acupuncture treatment. Should an infection occur, additional
                treatment, including antibiotics, may be necessary. NEEDLE
                SHOCK: Needle shock is a rare complication after an acupuncture
                treatment. Needle shock is a syndrome which occurs in a about 5%
                of acupuncture patients. It presents as general malaise, cold
                perspiration, nausea, and in extreme situations, loss of
                consciousness. ALLERGIC REACTIONS: In rare cases, an allergic
                reaction to the metal needle may require additional treatment.
                DELAYED HEALING: Delayed wound healing or wound disruption are a
                rare complication experienced by patients in the aftermath of an
                acupuncture treatment. There is a greater risk for smokers, who
                frequently have dry, sagging skin, which does not heal as
                readily as that of non-smokers.
              </p>
              <p class="mb-2">
                There are many variable conditions in addition to risk and
                potential complications that may influence the long term result
                from acupuncture facial treatments. Even though risks and
                complications occur infrequently, the risks cited are the ones
                that are particularly associated with an acupuncture facial
                treatment. Other complications and risks can occur but are even
                more uncommon. Should complications occur, other treatments may
                be necessary. The practice of acupuncture is not an exact
                science. Although good results are expected, there is no
                guarantee or warranty, either expressed or implied, on the
                results that may be obtained. DAMAGE TO DEEPER STRUCTURES:
                Deeper structures such as blood vessels and muscles are rarely
                damaged during the course of a facial acupuncture treatment. If
                this does occur, the injury may be temporary or permanent.
                ASYMMETRY: The human face is normally asymmetrical. Thus, there
                can be a variation from one side to the other in the results
                attained from a facial acupuncture treatment. BRUISING AND
                PUFFINESS: There is a possibility of bruising (hematomas),
                puffiness, blood, tingling, itching, warmth, pain or other
                symptoms at the site of the needle. NERVE INJURY: Injuries to
                the motor or sensory nerves rarely result from facial
                acupuncture treatments. Nerve injuries may cause temporary or
                permanent loss of facial movements and feeling. Such injuries
                may improve over time. Injury to sensory nerves of the face,
                neck and ear regions may cause temporary or more rarely
                permanent numbness. Painful nerve scarring is very rare.
                UNSATISFACTORY RESULT: There is the possibility of a poor result
                from an acupuncture facial. A patient may, from time to time, be
                disappointed with the results. LONG TERM EFFECTS: Subsequent
                alterations in facial appearance may occur as the result of the
                normal process of aging, weight loss or gain, sun exposure, or
                other circumstances not related to an acupuncture facial. An
                acupuncture facial does not arrest the aging process or produce
                permanent tightening of the face and neck. Future facial
                acupuncture maintenance treatments, or other treatments, may be
                necessary to maintain the results of an acupuncture facial.
              </p>
              <p class="mb-2">
                <span class="font-bold"
                  >OPEN SETTING, COMMUNICATIONS & RECORDING DEVICES - </span
                >All services will be provided in an open room setting where
                multiple patients will be receiving care. Patients may overhear
                some of other patients’ protected medical information during the
                course of care. Additionally, to the extent permitted by law, we
                may use cameras or other recording devices in a clinic. A notice
                will be posted at the clinic informing patients’ of the use of
                such devices.
              </p>
              <p class="mb-2">
                <span class="font-bold">METHODS OF CONTACTING YOU - </span>We
                may use your address, phone number, e-mail and clinical records
                to contact you with notifications, text messages, birthday and
                holiday related messages, health related information, or other
                topics as appropriate, based on the contact information you
                provided above. If contacting you by phone, we may leave a
                message on your answering machine or voicemail. You may still
                receive our services if you do not provide a phone number or
                email address.
              </p>
              <p class="mb-2">
                <span class="font-bold">DISCLAIMER - </span>Informed-consent
                documents are used to communicate information about the proposed
                procedure along with disclosure of risks. The informed consent
                process attempts to define principles of risk disclosure that
                should generally meet the needs of most patients in most
                circumstances. However, informed consent documents should not be
                considered all-inclusive in defining other methods of care and
                risks encountered. Our acupuncturists may provide patients with
                additional or different information which is based upon all the
                facts in each particular case and the present state of knowledge
                within the field of acupuncture. Informed consent documents are
                not intended to define or serve as the standard of acupuncture.
                Standards of acupuncture are determined on the basis of all of
                the facts involved in an individual case and are subject to
                change as scientific knowledge and technology advance and as
                practice patterns evolve. It is important that you read the
                above information carefully and have all of your questions
                answered before signing the following consent.
              </p>
              <p class="mb-2">
                <span class="font-bold">CONSENT FOR ACUPUNCTURE TREATMENT</span>
              </p>
              <ol class="ml-8 list-decimal">
                <li class="mb-2">
                  I acknowledge that no guarantee has been given by anyone as to
                  the results that may be obtained.
                </li>
                <li class="mb-2">
                  IT HAS BEEN EXPLAINED TO ME IN A WAY THAT I UNDERSTAND:
                  <ul class="mt-2 ml-4 list-disc">
                    <li class="mb-1">
                      THE ABOVE TREATMENT OR EXPOSURE TO BE UNDERTAKEN
                    </li>
                    <li class="mb-1">
                      THERE ARE RISKS TO THE PROCEDURE OR TREATMENT PROPOSED
                    </li>
                  </ul>
                </li>
                <li class="mb-2">
                  I hereby request and consent to the performance of acupuncture
                  treatments and other procedures within the scope of the
                  practice of acupuncture on me (or on the patient named below,
                  for whom I am legally responsible) by the licensed
                  acupuncturists practicing in the Modern Acupuncture clinic,
                  including those who now or in the future work at any Modern
                  Acupuncture clinic
                </li>
                <li class="mb-2">
                  If applicable, I WILL NOTIFY A CLINICAL STAFF MEMBER WHO IS
                  CARING FOR ME IF I AM OR BECOME PREGNANT.
                </li>
                <li class="mb-2">
                  While I do not expect the clinical staff to be able to
                  anticipate and explain all possible risks and complications of
                  treatment, I wish to rely on the clinical staff to exercise
                  judgment during the course of treatment which the clinical
                  staff thinks at the time, based upon the facts then known, is
                  in my best interest.
                </li>
                <li>
                  I understand that results are not guaranteed. I further
                  understand the clinical and administrative staff may review my
                  patient records and lab reports, but all my records will be
                  kept confidential and will not be released without my written
                  consent.
                </li>
              </ol>
            </div>
          </div>

          <div class="flex flex-wrap mb-2 mt-2">
            <div class="w-full md:w-1/5 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Initials
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="
                  $v.form.initial_informed_consent_for_acupuncture.$model
                "
                name="initial_informed_consent_for_acupuncture"
              />
              <div
                class="error"
                v-if="
                  $v.form.initial_informed_consent_for_acupuncture.$anyError &&
                  !$v.form.initial_informed_consent_for_acupuncture.required
                "
              >
                Initials is required
              </div>
            </div>

            <div class="w-full md:w-4/5 px-3 mt-4 mb-2 md:mb-0">
              <span class="text-gray-700 text-sm"
                >By voluntarily signing below, I show that I have read, or have
                had read to me, the above consent to treatment, have been told
                about the risks and benefits of acupuncture and have had an
                opportunity to ask questions. Additionally, I affirm that I
                understand and agree to all the terms and conditions mentioned
                above. I also affirm that all of my information listed in this
                form is accurate and correct to the best of my knowledge.</span
              >
            </div>
          </div>

          <div
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-4 bg-red-900 mb-4"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              ARBITRATION AGREEMENT
            </div>
          </div>
          <div class="block mt-4 mb-2">
            <div class="text-gray-600 mt-2">
              <p class="mb-2">
                By signing an arbitration agreement, a patient and a healthcare
                practitioner agree to use a private, confidential, and expedited
                arbitration, rather than a public, lengthy and costly courtroom
                trial, to settle any future malpractice claims. In arbitration,
                a neutral arbitrator (quite often a retired judge) decides the
                case. By agreeing to arbitrate, the parties preserve their right
                to present their claims fully; however, they choose a specific
                forum for dispute resolution: an arbitration hearingrather than
                a judge or jury trial.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 1: </span>Agreement to
                Arbitrate: It is understood that any dispute as to medical
                malpractice, including whether any medical services rendered
                under this contract were unnecessary or unauthorized or were
                improperly, negligently or incompetently rendered, will be
                determined by submission to arbitration as provided by state and
                federal law, and not by a lawsuit or resort to court process,
                except as state and federal law provides for judicial review of
                arbitration proceedings. Both parties to this contract, by
                entering into it, are giving up their constitutional right to
                have any such dispute decided in a court of law before a jury,
                and instead are accepting the use of arbitration. An acupuncture
                treatment involves the insertion of acupuncture needles at
                specific points on your body to boost your bodys natural
                painkillers, increase blood flow and trigger a healing response.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 2: </span>All Claims Must be
                Arbitrated: It is also understood that any dispute that does not
                relate to medical malpractice, including disputes as to whether
                or not a dispute is subject to arbitration, as to whether this
                agreement is unconscionable, and any procedural disputes, will
                also be determined by submission to binding arbitration. It is
                the intention of the parties that this agreement bind all
                parties as to all claims, including claims arising out of or
                relating to treatment or services provided by the health care
                provider, including any heirs or past, present or future
                spouse(s) of the patient in relation to all claims, including
                loss of consortium. This agreement is also intended to bind any
                children of the patient whether born or unborn at the time of
                the occurrence giving rise to any claim. This agreement is
                intended to bind the patient and the health care provider and/or
                other licensed health care providers, preceptors, or interns who
                now or in the future treat the patient while employed by,
                working or associated with or serving as a back-up for the
                health care provider, including those working at the health care
                provider’s clinic or office or any other clinic or office
                whether signatories to this form or not.
              </p>
              <p class="mb-2">
                All claims for monetary damages exceeding the jurisdictional
                limit of the small claims court against the health care
                provider, and/or the health care provider’s associates,
                association, corporation, partnership, employees, agents and
                estate, must be arbitrated including, without limitation, claims
                for loss of consortium, wrongful death, emotional distress,
                injunctive relief, or punitive damages. This agreement is
                intended to create an open book account unless and until
                revoked.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 3: </span>Procedures and
                Applicable Law: A demand for arbitration must be communicated in
                writing to all parties. Each party shall select an arbitrator
                (party arbitrator) within thirty days, and a third arbitrator
                (neutral arbitrator) shall be selected by the arbitrators
                appointed by the parties within thirty days thereafter. The
                neutral arbitrator shall then be the sole arbitrator and shall
                decide the arbitration. Each party to the arbitration shall pay
                such party’s pro rata share of the expenses and fees of the
                neutral arbitrator, together with other expenses of the
                arbitration incurred or approved by the neutral arbitrator, not
                including counsel fees, witness fees, or other expenses incurred
                by a party for such party’s own benefit.
              </p>
              <p class="mb-2">
                Either party shall have the absolute right to bifurcate the
                issues of liability and damage upon written request to the
                neutral arbitrator.
              </p>
              <p class="mb-2">
                The parties consent to the intervention and joinder in this
                arbitration of any person or entity that would otherwise be a
                proper additional party in a court action, and upon such
                intervention and joinder, any existing court action against such
                additional person or entity shall be stayed pending arbitration.
              </p>
              <p class="mb-2">
                The parties agree that provisions of state and federal law,
                where applicable, establishing the right to introduce evidence
                of any amount payable as a benefit to the patient to the maximum
                extent permitted by la, limiting the right to recover non-
                economic losses, and the right to have a judgment for future
                damages conformed to periodic payments, shall apply to disputes
                within this Arbitration Agreement. The parties further agree
                that the Commercial Arbitration Rules of the American
                Arbitration Association shall govern any arbitration conducted
                pursuant to this Arbitration Agreement.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 4: </span>General Provision: All
                claims based upon the same incident, transaction, or related
                circumstances shall be arbitrated in one proceeding. A claim
                shall be waived and forever barred if (1) on the date notice
                thereof is received, the claim, if asserted in a civil action,
                would be barred by the applicable legal statute of limitations,
                or (2) the claimant fails to pursue the arbitration claim in
                accordance with the procedures prescribed herein with reasonable
                diligence.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 5: </span>Revocation: This
                agreement may be revoked by written notice delivered to the
                health care provider within 30 days of signature and, if not
                revoked, will govern all professional services received by the
                patient and all other disputes between the parties.
              </p>
              <p class="mb-2">
                <span class="font-bold">Article 6: </span>Effective as of the
                date of first professional services. If any provision of this
                Arbitration Agreement is held invalid or unenforceable, the
                remaining provisions shall remain in full force and shall not be
                affected by the invalidity of any other provision. I understand
                that I have the right to receive a copy of this Arbitration
                Agreement. By my signature below, I acknowledge that I have
                received a copy. I understand that methods of treatment are
                limited to acupuncture. I have been informed that acupuncture is
                a generally safe method of treatment, but that it may have some
                side effects, including bruising, numbness or tingling near the
                needling sites that may last a few days, and dizziness or
                fainting. Unusual risks of acupuncture include spontaneous
                miscarriage, nerve damage and organ puncture, including lung
                puncture (pneumothorax). Infection is another possible risk,
                although the clinic uses sterile disposable needles and
                maintains a clean and safe environment. I understand that while
                this document describes the major risks of treatment, other side
                effects and risks may occur.
              </p>
            </div>
          </div>
          <div class="flex flex-wrap mb-2 mt-2">
            <div class="w-full md:w-1/5 mb-2 md:mb-0">
              <span class="text-gray-700 font-bold"
                >Initials
                <span class="text-red-500 text-bold text-xl">*</span></span
              >
              <input
                type="text"
                class="form-input mt-1 block w-full"
                v-model="$v.form.initial_retroactive_effect.$model"
                name="initial_retroactive_effect"
              />
              <div
                class="error"
                v-if="
                  $v.form.initial_retroactive_effect.$anyError &&
                  !$v.form.initial_retroactive_effect.required
                "
              >
                Initials is required
              </div>
            </div>

            <div class="w-full md:w-4/5 px-3 mt-4 mb-2 md:mb-0">
              <span class="text-gray-700 text-sm"
                >Retroactive Effect: If patient intends this agreement to cover
                services rendered before the date it is signed (for example,
                emergency treatment), patient should initial here.</span
              >
            </div>
          </div>

          <vue-signature-pad
            id="signature"
            width="50%"
            height="150px"
            saveType="image/svg+xml"
            ref="signaturePad"
            :options="signatureOptions"
          />
          <span class="text-blue-500 text-sm cursor-pointer" @click="clear"
            >clear</span
          >
          <div class="error" v-if="blankSignature">
            Signature is required
          </div>

          <div
            v-if="submitStatus === 'ERROR'"
            class="flex flex-wrap items-center justify-between px-1 py-1 mt-5 bg-red-500 mb-5"
          >
            <div
              class="font-bold text-xl leading-relaxed inline-block ml-10 whitespace-no-wrap uppercase text-white"
            >
              ERROR WHILE SUBMITTING THE FORM. PLEASE FILL IN REQUIRED FIELDS
            </div>
          </div>

          <div
            class="py-4 bg-grey-lightest mt-8 border-grey-lighter flex items-center"
          >
            <loading-button
              :loading="sending"
              class="btn bg-red-900 text-white text-xl pl-10 pr-10 pt-4 pb-4 hover:bg-red-800"
              type="submit"
              >Submit
            </loading-button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import LoadingButton from '../../Shared/LoadingButton'
  import { required, minLength } from 'vuelidate/lib/validators'

  export default {
    metaInfo: { title: 'Intake Form ' },
    components: {
      LoadingButton,
    },
    props: ['code'],
    data() {
      return {
        sending: false,
        signatureOptions: {
          // penColor: "#000"
        },
        submitStatus: null,
        blankSignature: false,
        form: {
          acknowledgement: null,
          first_name: null,
          last_name: null,
          gender: null,
          dob: null,
          age: null,
          email: null,
          mobile: null,
          address: null,
          city: null,
          state: null,
          postal_code: null,
          patient_occupation: null,
          emergency_contact_name: null,
          emergency_contact_phone: null,
          acupuncture_before: null,
          initial_patient_information: null,
          height: null,
          weight: null,
          blood_thinner_medicine: null,
          pregnancy: null,
          seizure_disorder: null,
          hiv: null,
          hepatitis_b: null,
          nickel_allergy: null,
          today_digestive_issue: null,
          today_stress: null,
          today_insomnia: null,
          today_anxiety: null,
          today_high_blood_pressure: null,
          today_depression: null,
          today_allergies: null,
          today_back_pain: null,
          today_muscle_sprain: null,
          today_headaches: null,
          today_fatigue: null,
          today_joint_pain: null,
          today_menstrual_cramps: null,
          today_muscle_aches: null,
          most_prominent_reason: null,
          current_symptoms_duration: null,
          under_physician_care: null,
          under_physician_care_diagnosis: null,
          botox_dermal_filler: null,
          cosmetic_surgical_procedures: null,
          tendency_to_bruise: null,
          migraine: null,
          head_injury: null,
          cosmetic_smile_lines: null,
          cosmetic_crows_feet: null,
          cosmetic_forehead_lines: null,
          cosmetic_surrounding_lips: null,
          cosmetic_lines_between_eyebrows: null,
          cosmetic_marionette_lines: null,
          privacy_acknowledge: null,
          initial_informed_consent_for_acupuncture: null,
          initial_retroactive_effect: null,
        },
      }
    },
    validations: {
      form: {
        acknowledgement: { required },
        first_name: { required },
        last_name: { required },
        gender: { required },
        dob: { required },
        age: { required },
        email: { required },
        mobile: { required },
        address: { required },
        city: { required },
        state: { required },
        postal_code: { required },
        patient_occupation: { required },
        emergency_contact_name: { required },
        emergency_contact_phone: { required },
        acupuncture_before: { required },
        initial_patient_information: { required },
        height: { required },
        weight: { required },
        blood_thinner_medicine: { required },
        pregnancy: { required },
        seizure_disorder: { required },
        hepatitis_b: { required },
        nickel_allergy: { required },
        privacy_acknowledge: { required },
        initial_informed_consent_for_acupuncture: { required },
        initial_retroactive_effect: { required },
      },
    },

    methods: {
      clear() {
        this.$refs.signaturePad.clearSignature()
      },
      submit() {
        this.$v.$touch()
        if (this.$v.$invalid) {
          this.submitStatus = 'ERROR'
        } else {
          this.sending = true

          const { isEmpty, data } = this.$refs.signaturePad.saveSignature()

          if (!isEmpty) {
            this.$inertia.post(route('intake-forms.store'), {
              code: this.code,
              model: this.form,
              signature: data,
            })
          } else {
            this.sending = false
            this.blankSignature = true
          }
        }
      },
    },
  }
</script>

<style scoped>
  #signature {
    border: double 3px transparent;
    border-radius: 5px;
    background-image: linear-gradient(white, white),
      radial-gradient(circle at top left, #4bc5e8, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
  }

  .error {
    color: red;
  }
</style>
