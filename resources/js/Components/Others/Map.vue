<template>
  <div ref="map" style="width: 100%; height: 600px"></div>
</template>

<script>
export default {
  name: "GoogleMap",
  props: {
    zoom: {
      type: Number,
      default: 13,
    },
    items: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      map: null,
      center: { lat: -23.55052, lng: -46.633308 },
      userLocation: null,
      locations: [],
    };
  },
  methods: {
    initMap() {
      this.map = new google.maps.Map(this.$refs.map, {
        center: this.center,
        zoom: this.zoom,
      });
    },
    requestCurrentLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          (position) => {
            this.userLocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude,
            };

            this.map.setCenter(this.userLocation);

            new google.maps.Marker({
              position: this.userLocation,
              map: this.map,
              title: "Você está aqui!",
              icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
            });

            // Após obter a localização, encontra o item mais próximo
            this.findClosestItem();
          },
          (error) => {
            console.error("Erro ao obter localização:", error);
          },
          {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0,
          }
        );
      } else {
        console.error("Geolocalização não é suportada pelo navegador.");
      }
    },
    findClosestItem() {
      if (!this.userLocation || !this.items.length) return;

      let closestItem = null;
      let closestDistance = Infinity;

      this.items.forEach((item) => {
        const itemLocation = new google.maps.LatLng(
          Number(item.latitude),
          Number(item.longitude)
        );
        const userLatLng = new google.maps.LatLng(
          this.userLocation.lat,
          this.userLocation.lng
        );

        // Calcula a distância entre o usuário e o item
        const distance = google.maps.geometry.spherical.computeDistanceBetween(
          userLatLng,
          itemLocation
        );

        if (distance < closestDistance) {
          closestDistance = distance;
          closestItem = item;
        }
      });

      if (closestItem) {
        console.log("Item mais próximo encontrado:", closestItem);
        this.drawRouteToClosestItem(closestItem);
      } else {
        console.log("Nenhum item próximo encontrado.");
      }
    },
    drawRouteToClosestItem(closestItem) {
      const directionsService = new google.maps.DirectionsService();
      const directionsRenderer = new google.maps.DirectionsRenderer();

      directionsRenderer.setMap(this.map);

      directionsService.route(
        {
          origin: this.userLocation,
          destination: {
            lat: Number(closestItem.latitude),
            lng: Number(closestItem.longitude),
          },
          travelMode: google.maps.TravelMode.DRIVING,
        },
        (response, status) => {
          if (status === "OK") {
            directionsRenderer.setDirections(response);
          } else {
            console.error("Erro ao calcular a rota:", status);
          }
        }
      );
    },
    getItemsBounds() {
      this.items.forEach((item) => {
        const marker = new google.maps.Marker({
          position: { lat: Number(item.latitude), lng: Number(item.longitude) },
          map: this.map,
          title: item.name,
        });

        const infoWindow = new google.maps.InfoWindow({
          content: `<div><strong class="text-black">${item.name}</strong></div>
        <div><strong class="text-black">${item.address}</strong></div>
        <div><strong class="text-black">${item.neighborhood}</strong></div>
          <div><strong class="text-black">${
            item.zip_code ? item.zip_code : ""
          }</strong></div>`,
        });

        marker.addListener("click", () => {
          infoWindow.open(this.map, marker);
        });
      });
    },
  },
  mounted() {
    this.initMap();
    this.requestCurrentLocation();
    this.getItemsBounds();
  },
};
</script>
